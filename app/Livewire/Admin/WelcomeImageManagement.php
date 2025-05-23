<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\WelcomeImage;
use Livewire\Attributes\On;

class WelcomeImageManagement extends Component
{
    use WithFileUploads;

    public $images;
    public $image;
    public $title;
    public $is_active = true;
    public $order = 0;
    public $welcomeImageId;
    public $editing = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'image' => 'nullable|image|max:2048',
        'is_active' => 'boolean',
        'order' => 'required|integer|min:0',
    ];

    public function mount()
    {
        $this->loadImages();
    }

    public function loadImages()
    {
        $this->images = WelcomeImage::with('media')
            ->orderBy('order')
            ->get();
    }

    public function saveImage()
    {
        $this->validate();

        if ($this->editing) {
            $welcomeImage = WelcomeImage::findOrFail($this->welcomeImageId);
            $message = 'Gambar berhasil diperbarui';
        } else {
            $welcomeImage = new WelcomeImage();
            $message = 'Gambar berhasil ditambahkan';
        }

        $welcomeImage->title = $this->title;
        $welcomeImage->is_active = $this->is_active;
        $welcomeImage->order = $this->order;
        $welcomeImage->save();

        if ($this->image) {
            $welcomeImage->clearMediaCollection('welcome_images');
            $welcomeImage->addMedia($this->image->getRealPath())
                ->usingName($this->title)
                ->toMediaCollection('welcome_images');
        }

        $this->resetForm();
        $this->loadImages();
        
        session()->flash('message', $message);
    }

    public function edit($id)
    {
        $welcomeImage = WelcomeImage::findOrFail($id);
        $this->welcomeImageId = $id;
        $this->title = $welcomeImage->title;
        $this->is_active = $welcomeImage->is_active;
        $this->order = $welcomeImage->order;
        $this->editing = true;
    }

    public function delete($id)
    {
        $welcomeImage = WelcomeImage::findOrFail($id);
        $welcomeImage->delete();
        $this->loadImages();
        session()->flash('message', 'Gambar berhasil dihapus');
    }

    public function toggleActive($id)
    {
        $welcomeImage = WelcomeImage::findOrFail($id);
        $welcomeImage->update([
            'is_active' => !$welcomeImage->is_active
        ]);
        $this->loadImages();
    }

    private function resetForm()
    {
        $this->reset(['title', 'image', 'is_active', 'order', 'welcomeImageId', 'editing']);
    }

    public function render()
    {
        return view('livewire.admin.welcome-image-management');
    }
}
