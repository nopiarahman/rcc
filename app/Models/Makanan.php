<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'makanan_size')
            ->withPivot('extra_price')
            ->withTimestamps();
    }
    public function activeDiscount()
    {
        $now = now();
        return $this->discounts()
            ->where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('discount_amount', 'desc')
            ->first();
    }
    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gambar')
            ->singleFile()
            ->registerMediaConversions(function (?Media $media = null) {
                $this->addMediaConversion('thumb')
                    ->width(150)
                    ->height(150);
                
                $this->addMediaConversion('preview')
                    ->width(300)
                    ->height(300);
            });
    }
}
