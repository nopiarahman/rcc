<?php

namespace App\Livewire\Admin;

use App\Models\DiscountCode;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class DiscountCodeManager extends Component
{
    use WithPagination;
    
    // Form properties
    public $code;
    public $name;
    public $description;
    public $discount_amount;
    public $discount_type = 'percentage';
    public $minimum_purchase = 0;
    public $max_redeem;
    public $start_date;
    public $end_date;
    public $is_active = true;
    
    // Edit mode
    public $editMode = false;
    public $discountCodeId = null;
    
    // Filter properties
    public $search = '';
    public $filterActive = 'all';
    
    // Modal states
    public $showForm = false;
    public $showDeleteConfirmation = false;
    
    protected function rules()
    {
        return [
            'code' => 'required|string|max:20|unique:discount_codes,code' . ($this->editMode ? ',' . $this->discountCodeId : ''),
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_amount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'minimum_purchase' => 'required|numeric|min:0',
            'max_redeem' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ];
    }
    
    public function mount()
    {
        // Initialize dates to today and a week from today
        $this->start_date = now()->format('Y-m-d\TH:i');
        $this->end_date = now()->addDays(7)->format('Y-m-d\TH:i');
    }
    
    public function render()
    {
        $discountCodesQuery = DiscountCode::when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterActive !== 'all', function ($query) {
                if ($this->filterActive === 'active') {
                    return $query->active();
                } elseif ($this->filterActive === 'inactive') {
                    $now = now();
                    return $query->where(function ($q) use ($now) {
                        $q->where('is_active', false)
                          ->orWhere('start_date', '>', $now)
                          ->orWhere('end_date', '<', $now)
                          ->orWhere(function ($subQ) {
                              $subQ->whereNotNull('max_redeem')
                                    ->whereRaw('used_count >= max_redeem');
                          });
                    });
                }
            })
            ->orderBy('created_at', 'desc');
        
        $discountCodes = $discountCodesQuery->paginate(10);
        
        return view('livewire.admin.discount-code-manager', [
            'discountCodes' => $discountCodes,
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
            'code', 'name', 'description', 'discount_amount', 'minimum_purchase',
            'max_redeem', 'editMode', 'discountCodeId'
        ]);
        
        $this->discount_type = 'percentage';
        $this->is_active = true;
        $this->start_date = now()->format('Y-m-d\TH:i');
        $this->end_date = now()->addDays(7)->format('Y-m-d\TH:i');
    }
    
    public function generateCode()
    {
        $this->code = DiscountCode::generateUniqueCode(8);
    }
    
    public function save()
    {
        $this->validate();
        
        try {
            DB::beginTransaction();
            
            $discountCodeData = [
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'description' => $this->description,
                'discount_amount' => $this->discount_amount,
                'discount_type' => $this->discount_type,
                'minimum_purchase' => $this->minimum_purchase,
                'max_redeem' => $this->max_redeem,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_active' => $this->is_active,
            ];
            
            if ($this->editMode) {
                // Edit mode - update existing discount code
                $discountCode = DiscountCode::findOrFail($this->discountCodeId);
                $discountCode->update($discountCodeData);
                $message = 'Discount code updated successfully!';
            } else {
                // Create mode - create new discount code
                DiscountCode::create($discountCodeData);
                $message = 'Discount code created successfully!';
            }
            
            DB::commit();
            
            $this->closeForm();
            session()->flash('success', $message);
            $this->dispatch('discount-code-saved');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        $discountCode = DiscountCode::findOrFail($id);
        
        $this->discountCodeId = $discountCode->id;
        $this->code = $discountCode->code;
        $this->name = $discountCode->name;
        $this->description = $discountCode->description;
        $this->discount_amount = $discountCode->discount_amount;
        $this->discount_type = $discountCode->discount_type;
        $this->minimum_purchase = $discountCode->minimum_purchase;
        $this->max_redeem = $discountCode->max_redeem;
        $this->start_date = $discountCode->start_date->format('Y-m-d\TH:i');
        $this->end_date = $discountCode->end_date->format('Y-m-d\TH:i');
        $this->is_active = $discountCode->is_active;
        
        $this->editMode = true;
        $this->showForm = true;
    }
    
    public function confirmDelete($id)
    {
        $this->discountCodeId = $id;
        $this->showDeleteConfirmation = true;
    }
    
    public function cancelDelete()
    {
        $this->discountCodeId = null;
        $this->showDeleteConfirmation = false;
    }
    
    public function delete()
    {
        try {
            $discountCode = DiscountCode::findOrFail($this->discountCodeId);
            
            // Check if the discount code has been used
            if ($discountCode->used_count > 0) {
                session()->flash('error', 'Cannot delete discount code that has been used.');
                $this->showDeleteConfirmation = false;
                $this->discountCodeId = null;
                return;
            }
            
            $discountCode->delete();
            
            session()->flash('success', 'Discount code deleted successfully!');
            $this->showDeleteConfirmation = false;
            $this->discountCodeId = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function toggleActive($id)
    {
        try {
            $discountCode = DiscountCode::findOrFail($id);
            $discountCode->update([
                'is_active' => !$discountCode->is_active
            ]);
            
            session()->flash('success', 'Discount code status updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function duplicate($id)
    {
        try {
            $originalCode = DiscountCode::findOrFail($id);
            
            $newCode = $originalCode->replicate();
            $newCode->code = DiscountCode::generateUniqueCode(8);
            $newCode->name = $originalCode->name . ' (Copy)';
            $newCode->used_count = 0;
            $newCode->is_active = false; // Default to inactive for safety
            $newCode->save();
            
            session()->flash('success', 'Discount code duplicated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
}
