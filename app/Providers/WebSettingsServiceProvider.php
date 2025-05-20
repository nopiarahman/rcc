<?php

namespace App\Providers;

use App\Models\WebSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class WebSettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share web settings with all views
        View::composer('*', function ($view) {
            static $settings = null;
            
            if (is_null($settings)) {
                $settings = WebSetting::firstOrCreate([], [
                    'site_name' => 'Raihaan Coffee Corner',
                    'theme' => 'green',
                ]);
            }
            
            $view->with('webSettings', $settings);
        });
    }
}

if (!function_exists('web_setting')) {
    /**
     * Get a web setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function web_setting($key = null, $default = null)
    {
        $settings = app(WebSetting::class);
        
        if (is_null($key)) {
            return $settings;
        }
        
        return $settings->$key ?? $default;
    }
}
