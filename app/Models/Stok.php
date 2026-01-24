<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stok extends Model
{
    protected $table = 'stok';
    protected $primaryKey = 'id_stok';
    public $timestamps = true;

    protected $fillable = [
        'id_produk',
        'stok',
        'tgl_penerimaan',
        'tgl_kadaluwarsa'
    ];

    protected $casts = [
        'tgl_penerimaan' => 'date',
        'tgl_kadaluwarsa' => 'date'
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
