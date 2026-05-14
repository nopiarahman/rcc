<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotolanUkuran extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'botolan_ukurans';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'harga'     => 'integer',
        'is_active' => 'boolean',
    ];

    public function botolanProduk(): BelongsTo
    {
        return $this->belongsTo(BotolanProduk::class, 'botolan_produk_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('foto')->singleFile();
    }
}
