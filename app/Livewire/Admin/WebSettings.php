<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ThemeColor;
use App\Models\WebSetting;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class WebSettings extends Component
{
    use WithFileUploads;

    public $site_name;
    public $tagline;
    public $logo;
    public $favicon;
    public $current_logo;
    public $current_favicon;
    public $theme;
    public $selectedTheme;
    public $latitude;
    public $longitude;
    public $delivery_radius;
    public $order_mode;
    public $whatsapp_number;
    public $settings;
    public $themeColors = [];
    public $selectedThemeColor = null;
    
    // Opening hours and temporary closure
    public $opening_time;
    public $closing_time;
    public $is_temporarily_closed = false;
    public $temporary_closure_message;
    public $ongkir_enabled = false;
    public $ongkir_per_km = 0;
    public $ongkir_free_km = 0;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public array $availableOrderModes = [
        'both' => 'Keduanya (Delivery & Takeaway)',
        'delivery' => 'Hanya Delivery',
        'takeaway' => 'Hanya Takeaway',
    ];

    public function mount()
    {
        $this->themeColors = ThemeColor::where('is_active', true)->get();
        $this->settings = WebSetting::firstOrCreate([], [
            'site_name' => 'Raihaan Coffee Corner',
            'theme' => 'green',
            'latitude' => -1.66651,
            'longitude' => 103.65238,
            'delivery_radius' => 600,
            'order_mode' => 'dine_in',
            'whatsapp_number' => '6281234567890',
            'opening_time' => '08:00:00',
            'closing_time' => '22:00:00',
            'is_temporarily_closed' => false,
            'temporary_closure_message' => null,
        ]);
        
        // Initialize the time fields with the current settings
        $this->opening_time = $this->settings->opening_time ? $this->settings->opening_time->format('H:i') : '08:00';
        $this->closing_time = $this->settings->closing_time ? $this->settings->closing_time->format('H:i') : '22:00';
        $this->is_temporarily_closed = $this->settings->is_temporarily_closed ?? false;
        $this->temporary_closure_message = $this->settings->temporary_closure_message;
        $this->ongkir_enabled = $this->settings->ongkir_enabled ?? false;
        $this->ongkir_per_km = $this->settings->ongkir_per_km ?? 0;
        $this->ongkir_free_km = $this->settings->ongkir_free_km ?? 0;

        $this->site_name = $this->settings->site_name;
        $this->tagline = $this->settings->tagline;
        $this->current_logo = $this->settings->logo_path;
        $this->current_favicon = $this->settings->favicon_path;
        $this->theme = $this->settings->theme;
        $this->selectedTheme = $this->settings->theme;
        $this->latitude = $this->settings->latitude;
        $this->longitude = $this->settings->longitude;
        $this->delivery_radius = $this->settings->delivery_radius;
        $this->order_mode = $this->settings->order_mode;
        $this->whatsapp_number = $this->settings->whatsapp_number;

        $this->selectedThemeColor = $this->themeColors->firstWhere('name', $this->theme) ?? $this->themeColors->first();
    }

    protected function rules()
    {
        return [
            'site_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|dimensions:min_width=32,min_height=32,max_width=192,max_height=192',
            'theme' => 'required|string|exists:theme_colors,name',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'delivery_radius' => 'required|integer|min:100',
            'order_mode' => 'required|in:both,delivery,takeaway',
            'whatsapp_number' => 'required|string|max:20',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'is_temporarily_closed' => 'boolean',
            'temporary_closure_message' => 'nullable|string|max:500',
            'ongkir_enabled' => 'boolean',
            'ongkir_per_km' => 'required_if:ongkir_enabled,true|numeric|min:0',
            'ongkir_free_km' => 'nullable|numeric|min:0',
        ];
    }

    public function updatedSelectedTheme($value)
    {
        $this->theme = $value;
        $this->selectedThemeColor = $this->themeColors->firstWhere('name', $value);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'site_name' => $this->site_name,
            'tagline' => $this->tagline,
            'theme' => $this->selectedTheme,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'delivery_radius' => $this->delivery_radius,
            'order_mode' => $this->order_mode,
            'whatsapp_number' => $this->whatsapp_number,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'is_temporarily_closed' => $this->is_temporarily_closed,
            'temporary_closure_message' => $this->temporary_closure_message,
            'ongkir_enabled' => $this->ongkir_enabled,
            'ongkir_per_km' => $this->ongkir_per_km,
            'ongkir_free_km' => $this->ongkir_free_km,
        ];

        if ($this->logo) {
            // Delete old logo if exists
            if ($this->settings->logo_path) {
                Storage::delete('public/' . $this->settings->logo_path);
            }
            $path = $this->logo->store('public/logos');
            $data['logo_path'] = str_replace('public/', '', $path);
        }

        if ($this->favicon) {
            // Delete old favicon if exists
            if ($this->settings->favicon_path && Storage::disk('public')->exists($this->settings->favicon_path)) {
                Storage::disk('public')->delete($this->settings->favicon_path);
            }
            $faviconPath = $this->favicon->store('settings/favicon', 'public');
            $data['favicon_path'] = $faviconPath;
            $this->current_favicon = $faviconPath;
        }

        $this->settings->update($data);
        
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
