<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    const KATEGORI_DISPLAY = 'display';
    const KATEGORI_NON_DISPLAY = 'non-display';
    
    const JENIS_MAKANAN = 'makanan';
    const JENIS_MINUMAN = 'minuman';
    
    protected $fillable = ['nama', 'satuan', 'harga_satuan', 'kategori', 'jenis'];
    
    protected $guarded = ['id','created_at','updated_at'];
    public function minuman()
    {
        return $this->belongsToMany(Minuman::class, 'minuman_bahan')
                    ->withPivot('jumlah')
                    ->withTimestamps();
    }
}
