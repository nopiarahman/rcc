<?php

namespace App\Livewire\Admin;

use App\Models\Popup;
use Livewire\Component;
use Livewire\WithFileUploads;

class PopupManagement extends Component
{
    use WithFileUploads;

    public $popups;
    public $image;
    public $title;
    public $content;
    public $type       = 'text';
    public $is_active  = true;
    public $start_date;
    public $end_date;
    public $order      = 0;
    public $popupId;
    public $editing    = false;

    protected $rules = [
        'title'      => 'nullable|string|max:255',
        'content'    => 'nullable|string',
        'type'       => 'required|in:text,image',
        'is_active'  => 'boolean',
        'start_date' => 'nullable|date',
        'end_date'   => 'nullable|date|after_or_equal:start_date',
        'order'      => 'required|integer|min:0',
        'image'      => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $this->loadPopups();
    }

    public function loadPopups()
    {
        $this->popups = Popup::with('media')->orderBy('order')->get();
    }

    public function savePopup()
    {
        $this->validate();

        if ($this->editing) {
            $popup = Popup::findOrFail($this->popupId);
        } else {
            $popup = new Popup();
        }

        $popup->title      = $this->title;
        $popup->content    = $this->content;
        $popup->type       = $this->type;
        $popup->is_active  = $this->is_active;
        $popup->start_date = $this->start_date ?: null;
        $popup->end_date   = $this->end_date ?: null;
        $popup->order      = $this->order;
        $popup->save();

        if ($this->image) {
            $popup->clearMediaCollection('popups');
            $popup->addMedia($this->image->getRealPath())
                  ->usingName($this->title ?? 'popup-' . $popup->id)
                  ->toMediaCollection('popups');
        }

        $this->resetFields();
        $this->loadPopups();
    }

    public function editPopup($id)
    {
        $popup = Popup::with('media')->findOrFail($id);
        $this->popupId    = $popup->id;
        $this->title      = $popup->title;
        $this->content    = $popup->content;
        $this->type       = $popup->type;
        $this->is_active  = $popup->is_active;
        $this->start_date = $popup->start_date?->format('Y-m-d');
        $this->end_date   = $popup->end_date?->format('Y-m-d');
        $this->order      = $popup->order;
        $this->editing    = true;
    }

    public function deletePopup($id)
    {
        Popup::with('media')->findOrFail($id)->delete();
        $this->loadPopups();
    }

    public function resetFields()
    {
        $this->reset(['title', 'content', 'image', 'start_date', 'end_date', 'popupId', 'editing']);
        $this->type      = 'text';
        $this->is_active = true;
        $this->order     = 0;
    }

    public function render()
    {
        return view('livewire.admin.popup-management');
    }
}
