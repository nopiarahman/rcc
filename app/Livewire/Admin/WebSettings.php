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
        ]);
        
        $this->site_name = $this->settings->site_name;
        $this->current_logo = $this->settings->logo_path;
        $this->current_favicon = $this->settings->favicon_path;
        $this->theme = $this->settings->theme;
        $this->selectedTheme = $this->settings->theme;
    }

    protected $rules = [
        'site_name' => 'required|string|max:255',
        'logo' => 'nullable|image|max:2048',
        'favicon' => 'nullable|image|dimensions:min_width=32,min_height=32,max_width=192,max_height=192',
        'theme' => 'required|in:green,brown,yellow,blue,orange',
    ];

    public function updatedTheme($value)
    {
        $this->selectedTheme = $value;
    }

    public function save()
    {
        $this->validate();

        $settings = $this->settings;
        $settings->site_name = $this->site_name;
        $settings->theme = $this->selectedTheme;

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
