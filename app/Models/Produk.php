<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id_produk',
        'nama_produk',
        'harga',
        'status',
        'image_url',
        'id_kategori'
    ];

    protected $casts = [
        'status' => 'boolean',
        'harga' => 'decimal:2'
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function stok(): HasOne
    {
        return $this->hasOne(Stok::class, 'id_produk', 'id_produk');
    }
}
