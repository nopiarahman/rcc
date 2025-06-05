<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WebSetting;

class ThemeColor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'primary_color',
        'secondary_color',
        'button_bg_color',
        'button_text_color',
        'card_bg_color',
        'text_color',
        'muted_text_color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function webSettings()
    {
        return $this->hasMany(WebSetting::class, 'theme');
    }
}
