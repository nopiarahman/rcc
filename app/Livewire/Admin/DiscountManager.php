<?php

namespace App\Livewire\Admin;

use App\Models\Discount;
use App\Models\Minuman;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class DiscountManager extends Component
{
    use WithPagination;
    
    // Form properties
    public $minuman_id;
    public $name;
    public $description;
    public $discount_amount;
    public $discount_type = 'percentage';
    public $start_date;
    public $end_date;
    public $is_active = true;
    public $apply_to_all = false;
    
    // Edit mode
    public $editMode = false;
    public $discountId = null;
    
    // Filter properties
    public $search = '';
    public $filterActive = 'all';
    
    // Modal states
    public $showForm = false;
    public $showDeleteConfirmation = false;
    
    protected function rules()
    {
        return [
            'minuman_id' => $this->apply_to_all ? 'nullable' : 'required|exists:minumans,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_amount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'apply_to_all' => 'boolean',
        ];
    }
    
    public function mount()
    {
        // Initialize dates to today and a week from today
        $this->start_date = now()->format('Y-m-d\TH:i');
        $this->end_date = now()->addDays(3)->format('Y-m-d\TH:i');
    }
    
    public function render()
    {
        $minumans = Minuman::orderBy('nama')->get();
        
        $discountsQuery = Discount::with('minuman')
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('minuman', function ($mq) {
                          $mq->where('nama', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->filterActive !== 'all', function ($query) {
                if ($this->filterActive === 'active') {
                    $now = now();
                    return $query->where('is_active', true)
                                ->where('start_date', '<=', $now)
                                ->where('end_date', '>=', $now);
                } elseif ($this->filterActive === 'inactive') {
                    $now = now();
                    return $query->where(function ($q) use ($now) {
                        $q->where('is_active', false)
                          ->orWhere('start_date', '>', $now)
                          ->orWhere('end_date', '<', $now);
                    });
                }
            })
            ->orderBy('created_at', 'desc');
        
        $discounts = $discountsQuery->paginate(10);
        
        return view('livewire.admin.discount-manager', [
            'discounts' => $discounts,
            'minumans' => $minumans,
        ])->layout('components.layouts.app');
    }
    
    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }
    
    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }
    
    public function resetForm()
    {
        $this->reset([
            'minuman_id', 'name', 'description', 'discount_amount',
            'editMode', 'discountId', 'apply_to_all'
        ]);
        
        $this->discount_type = 'percentage';
        $this->is_active = true;
        $this->start_date = now()->format('Y-m-d\TH:i');
        $this->end_date = now()->addDays(3)->format('Y-m-d\TH:i');
    }
    
    public function save()
    {
        $this->validate();
        
        try {
            DB::beginTransaction();
            
            $discountData = [
                'name' => $this->name,
                'description' => $this->description,
                'discount_amount' => $this->discount_amount,
                'discount_type' => $this->discount_type,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_active' => $this->is_active,
            ];
            
            if ($this->editMode) {
                // Edit mode - update a single discount
                $discount = Discount::findOrFail($this->discountId);
                $discount->update($discountData);
                $message = 'Discount updated successfully!';
            } else {
                // Create mode
                if ($this->apply_to_all) {
                    // Apply to all products
                    $minumans = Minuman::all();
                    $count = 0;
                    
                    foreach ($minumans as $minuman) {
                        $discountData['minuman_id'] = $minuman->id;
                        Discount::create($discountData);
                        $count++;
                    }
                    
                    $message = "Discount applied to {$count} products successfully!";
                } else {
                    // Apply to a single product
                    $discountData['minuman_id'] = $this->minuman_id;
                    Discount::create($discountData);
                    $message = 'Discount created successfully!';
                }
            }
            
            DB::commit();
            
            $this->closeForm();
            session()->flash('success', $message);
            $this->dispatch('discount-saved');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        $discount = Discount::findOrFail($id);
        
        $this->discountId = $discount->id;
        $this->minuman_id = $discount->minuman_id;
        $this->name = $discount->name;
        $this->description = $discount->description;
        $this->discount_amount = $discount->discount_amount;
        $this->discount_type = $discount->discount_type;
        $this->start_date = $discount->start_date->format('Y-m-d\TH:i');
        $this->end_date = $discount->end_date->format('Y-m-d\TH:i');
        $this->is_active = $discount->is_active;
        
        $this->editMode = true;
        $this->showForm = true;
    }
    
    public function confirmDelete($id)
    {
        $this->discountId = $id;
        $this->showDeleteConfirmation = true;
    }
    
    public function cancelDelete()
    {
        $this->discountId = null;
        $this->showDeleteConfirmation = false;
    }
    
    public function delete()
    {
        try {
            $discount = Discount::findOrFail($this->discountId);
            $discount->delete();
            
            session()->flash('success', 'Discount deleted successfully!');
            $this->showDeleteConfirmation = false;
            $this->discountId = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function toggleActive($id)
    {
        try {
            $discount = Discount::findOrFail($id);
            $discount->update([
                'is_active' => !$discount->is_active
            ]);
            
            session()->flash('success', 'Discount status updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
}
