<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'sizes';
    protected $guarded = ['id','created_at','updated_at'];

    public function minuman()
    {
        return $this->belongsToMany(Minuman::class, 'minuman_size')
                    ->withPivot('extra_price')
                    ->withTimestamps();
    }
}
