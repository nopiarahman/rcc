<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sugar extends Model
{
    protected $table = 'sugars';
    protected $guarded = ['id','created_at','updated_at'];

    public function minuman()
    {
        return $this->belongsToMany(Minuman::class, 'minuman_sugar')
                    ->withPivot('extra_price')
                    ->withTimestamps();
    }
}
