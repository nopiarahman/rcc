<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanans';
    
    protected $fillable = [
        'nomor_pesanan',
        'user_id',
        'session_id',
        'nama_pemesan',
        'alamat_pengantaran',
        'waktu_pengantaran',
        'catatan',
        'total',
        'total_harga',
        'status',
    ];

    protected $casts = [
        'total' => 'integer',
        'total_harga' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pesanan) {
            if (empty($pesanan->nomor_pesanan)) {
                $pesanan->nomor_pesanan = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
            
            if (empty($pesanan->status)) {
                $pesanan->status = 'menunggu_konfirmasi';
            }
        });
    }

    public function details(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
