<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Makanan extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'makanans';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = ['is_habis' => 'boolean'];

    public function bahans()
    {
        return $this->belongsToMany(Bahan::class, 'makanan_bahan')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    public function toppings()
    {
        return $this->belongsToMany(Topping::class, 'makanan_topping')
            ->withPivot('extra_price')
            ->withTimestamps();
    }
}
