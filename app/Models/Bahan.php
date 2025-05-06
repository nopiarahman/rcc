<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    public function minuman()
    {
        return $this->belongsToMany(Minuman::class, 'minuman_bahan')
                    ->withPivot('jumlah')
                    ->withTimestamps();
    }
}
