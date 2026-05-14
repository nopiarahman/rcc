<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotolanProduk extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'botolan_produks';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = ['is_active' => 'boolean'];

    public function minuman(): BelongsTo
    {
        return $this->belongsTo(Minuman::class);
    }

    public function ukurans(): HasMany
    {
        return $this->hasMany(BotolanUkuran::class, 'botolan_produk_id')
            ->where('is_active', true)->orderBy('harga');
    }

    public function allUkurans(): HasMany
    {
        return $this->hasMany(BotolanUkuran::class, 'botolan_produk_id')->orderBy('harga');
    }

    public function getHargaMulaiAttribute(): int
    {
        return $this->ukurans()->min('harga') ?? 0;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('foto')->singleFile();
    }
}
