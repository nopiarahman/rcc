<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class Minuman extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = 'minumans';
    protected $guarded = ['id','created_at','updated_at'];
    protected $casts = ['is_habis' => 'boolean'];

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'minuman_size')
                    ->withPivot('extra_price')
                    ->withTimestamps();
    }

    public function sugars()
    {
        return $this->belongsToMany(Sugar::class, 'minuman_sugar')
                    ->withPivot('extra_price')
                    ->withTimestamps();
    }

    public function toppings()
    {
        return $this->belongsToMany(Topping::class, 'minuman_topping')
                    ->withPivot('extra_price')
                    ->withTimestamps();
    }

    public function bahans()
    {
        return $this->belongsToMany(Bahan::class, 'minuman_bahan')
                    ->withPivot('jumlah')
                    ->withTimestamps();
    }
    public function getHppAttribute()
    {
        return $this->bahans->sum(function ($bahan) {
            return $bahan->pivot->jumlah * $bahan->harga_satuan;
        });
    }
    public function defaultSize()
    {
        return $this->belongsTo(Size::class, 'default_size_id');
    }

    public function defaultSugar()
    {
        return $this->belongsTo(Sugar::class, 'default_sugar_id');
    }

    public function defaultTopping()
    {
        return $this->belongsTo(Topping::class, 'default_topping_id');
    }
    
    /**
     * Get the discounts for the minuman.
     */
    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }
    
    /**
     * Get the active discount for the minuman.
     */
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
    
    /**
     * Get the default price with default options (size, sugar, topping).
     */
    public function getDefaultPriceAttribute()
    {
        return \App\Helpers\DrinkPriceHelper::calculate($this);
    }
    
    /**
     * Get the discounted price for the minuman with default options.
     */
    public function getDiscountedPriceAttribute()
    {
        $activeDiscount = $this->activeDiscount();
        $defaultPrice = $this->default_price;

        if (!$activeDiscount) {
            return $defaultPrice;
        }

        return $activeDiscount->calculateDiscountedPrice($defaultPrice);
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('foto')
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

    /**
     * Get the first media URL with fallback to custom directory
     */
    public function getFirstMediaUrlWithFallback($conversionName = ''): string
    {
        // Try to get from Spatie Media Library first
        $url = $this->getFirstMediaUrl('foto', $conversionName);

        if ($url) {
            return $url;
        }

        // Fallback to custom directory if not found in Media Library
        $customDir = storage_path('app/public/minuman_images/');
        $matches = glob($customDir . $this->id . '*.jpg');
        if (!empty($matches)) {
            return url('storage/minuman_images/' . basename($matches[0]));
        }

        return asset('images/no-image.png');
    }
}
