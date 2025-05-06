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
}
