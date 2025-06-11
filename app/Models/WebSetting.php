<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    protected $fillable = [
        'site_name',
        'tagline',
        'logo_path',
        'favicon_path',
        'theme',
        'latitude',
        'longitude',
        'delivery_radius',
        'order_mode',
        'whatsapp_number',
        'opening_time',
        'closing_time',
        'is_temporarily_closed',
        'temporary_closure_message',
    ];

    protected $with = ['themeColor'];

    public function themeColor()
    {
        return $this->belongsTo(ThemeColor::class, 'theme', 'name');
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
        'is_temporarily_closed' => 'boolean',
    ];
}
