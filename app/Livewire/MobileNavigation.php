<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WebSetting;

class MobileNavigation extends Component
{
    public function render()
    {
        $settings = WebSetting::firstOrCreate([], [
            'site_name' => 'Raihaan Coffee Corner',
            'theme' => 'green',
        ]);

        $themeColors = $this->getThemeColors($settings->theme);
        
        return view('livewire.mobile-navigation', [
            'themeColors' => $themeColors,
            'settings' => $settings
        ]);
    }

    protected function getThemeColors($theme)
    {
        $colors = [
            'green' => [
                'background' => '#011a0f',
                'text' => '#ffffff',
                'accent' => '#4caf50',
                'badge' => '#ff5722',
            ],
            'brown' => [
                'background' => '#3e2723',
                'text' => '#efebe9',
                'accent' => '#8d6e63',
                'badge' => '#ff7043',
            ],
            'yellow' => [
                'background' => '#ff6f00',
                'text' => '#212121',
                'accent' => '#ffc107',
                'badge' => '#e91e63',
            ],
            'blue' => [
                'background' => '#0d47a1',
                'text' => '#e3f2fd',
                'accent' => '#2196f3',
                'badge' => '#ff9800',
            ],
            'orange' => [
                'background' => '#e65100',
                'text' => '#fff3e0',
                'accent' => '#ff9800',
                'badge' => '#2196f3',
            ],
        ];

        return $colors[$theme] ?? $colors['green'];
    }
}
