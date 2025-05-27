<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Storage;

class WebSettings extends Component
{
    use WithFileUploads;

    public $site_name;
    public $logo;
    public $favicon;
    public $current_logo;
    public $current_favicon;
    public $theme = 'green';
    public $selectedTheme = 'green';
    public $latitude;
    public $longitude;
    public $delivery_radius;
    public $whatsapp_number;
    public $order_mode = 'both';
    public $availableThemes = [
        'green' => 'Hijau (Default)',
        'brown' => 'Coklat Kopi',
        'yellow' => 'Kuning',
        'blue' => 'Biru',
        'orange' => 'Oranye',
    ];
    public $settings;

    public function mount()
    {
        $this->settings = WebSetting::firstOrCreate([], [
            'site_name' => 'Raihaan Coffee Corner',
            'theme' => 'green',
            'latitude' => -1.66651,
            'longitude' => 103.65238,
            'delivery_radius' => 600,
            'whatsapp_number' => '0',
            'order_mode' => 'both',
        ]);
        
        $this->site_name = $this->settings->site_name;
        $this->current_logo = $this->settings->logo_path;
        $this->current_favicon = $this->settings->favicon_path;
        $this->theme = $this->settings->theme;
        $this->selectedTheme = $this->settings->theme;
        $this->latitude = $this->settings->latitude;
        $this->longitude = $this->settings->longitude;
        $this->delivery_radius = $this->settings->delivery_radius;
        $this->whatsapp_number = $this->settings->whatsapp_number;
        $this->order_mode = $this->settings->order_mode;
    }

    protected $rules = [
        'site_name' => 'required|string|max:255',
        'logo' => 'nullable|image|max:2048',
        'favicon' => 'nullable|image|dimensions:min_width=32,min_height=32,max_width=192,max_height=192',
        'theme' => 'required|in:green,brown,yellow,blue,orange',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'delivery_radius' => 'required|integer|min:100',
        'whatsapp_number' => 'required|string|max:20',
        'order_mode' => 'required|in:delivery,takeaway,both',
    ];

    public function updatedTheme($value)
    {
        $this->selectedTheme = $value;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'site_name' => $this->site_name,
            'theme' => $this->selectedTheme,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'delivery_radius' => $this->delivery_radius,
            'whatsapp_number' => $this->whatsapp_number,
            'order_mode' => $this->order_mode,
        ];

        $settings = $this->settings;
        $settings->update($data);

        // Handle logo upload
        if ($this->logo) {
            // Delete old logo if exists
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $logoPath = $this->logo->store('settings/logo', 'public');
            $settings->logo_path = $logoPath;
            $this->current_logo = $logoPath;
        }

        // Handle favicon upload
        if ($this->favicon) {
            // Delete old favicon if exists
            if ($settings->favicon_path && Storage::disk('public')->exists($settings->favicon_path)) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            $faviconPath = $this->favicon->store('settings/favicon', 'public');
            $settings->favicon_path = $faviconPath;
            $this->current_favicon = $faviconPath;
        }

        $settings->save();
        
        session()->flash('message', 'Settings saved successfully.');
    }

    public function getThemeGradient($theme)
    {
        $gradients = [
            'green' => 'linear-gradient(to right, #011a0f, #006a3e)',
            'brown' => 'linear-gradient(to right, #3e2723, #8d6e63)',
            'yellow' => 'linear-gradient(to right, #ff6f00, #ffc107)',
            'blue' => 'linear-gradient(to right, #0d47a1, #2196f3)',
            'orange' => 'linear-gradient(to right, #e65100, #ff9800)',
        ];
        
        return $gradients[$theme] ?? $gradients['green'];
    }

    public function render()
    {
        return view('livewire.admin.web-settings');
    }
}
