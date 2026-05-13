<?php

namespace App\Livewire\Admin;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

class BannerManagement extends Component
{
    use WithFileUploads;

    public $banners;
    public $image;
    public $title;
    public $description;
    public $status = true;
    public $order = 0;
    public $bannerId;
    public $editing = false;
    public $showForm = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'image' => 'nullable|image|max:2048',
        'status' => 'boolean',
        'order' => 'required|integer|min:0',
    ];

    public function mount()
    {
        $this->loadBanners();
    }

    public function loadBanners()
    {
        $this->banners = Banner::with('media')
            ->orderBy('order')
            ->get();
    }

    public function saveBanner()
    {
        $this->validate();

        if ($this->editing) {
            $banner = Banner::findOrFail($this->bannerId);
        } else {
            $banner = new Banner();
        }

        $banner->title = $this->title;
        $banner->description = $this->description;
        $banner->status = $this->status;
        $banner->order = $this->order;
        $banner->save();

        // Handle image upload
        if ($this->image) {
            $banner->clearMediaCollection('banners');
            $banner->addMedia($this->image->getRealPath())
                  ->usingName($this->title)
                  ->toMediaCollection('banners');
        }

        $this->resetFields();
        $this->loadBanners();
        $this->dispatch('banner-updated');
    }

    public function openForm()
    {
        $this->resetFields();
        $this->showForm = true;
    }

    public function editBanner($id)
    {
        $banner = Banner::with('media')->findOrFail($id);
        $this->bannerId = $banner->id;
        $this->title = $banner->title;
        $this->description = $banner->description;
        $this->status = $banner->status;
        $this->order = $banner->order;
        $this->editing = true;
        $this->showForm = true;
    }

    public function deleteBanner($id)
    {
        $banner = Banner::with('media')->findOrFail($id);
        $banner->delete();
        $this->loadBanners();
        $this->dispatch('banner-updated');
    }

    public function resetFields()
    {
        $this->reset(['title', 'description', 'image', 'status', 'order', 'bannerId', 'editing', 'showForm']);
        $this->status = true;
        $this->order = 0;
    }

    public function render()
    {
        return view('livewire.admin.banner-management');
    }
}
