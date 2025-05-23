<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesananDetail extends Model
{
    protected $table = 'pesanan_details';
    
    protected $fillable = [
        'pesanan_id',
        'minuman_id',
        'nama_minuman',
        'harga',
        'qty',
        'size',
        'gula',
        'topping',
        'catatan',
        'subtotal',
    ];

    protected $casts = [
        'harga' => 'integer',
        'qty' => 'integer',
        'subtotal' => 'integer',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function minuman()
    {
        return $this->belongsTo(Minuman::class, 'minuman_id');
    }
}
