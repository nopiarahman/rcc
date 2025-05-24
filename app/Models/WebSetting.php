<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    protected $fillable = [
        'site_name',
        'logo_path',
        'favicon_path',
        'theme',
        'latitude',
        'longitude',
        'delivery_radius',
        'whatsapp_number',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
