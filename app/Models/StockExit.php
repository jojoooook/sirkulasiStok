<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockExit extends Model
{

    protected $fillable = [
        'kode_barang',
        'stok_keluar',
        'tanggal_keluar',
        'keterangan',
        'nomor_nota',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'kode_barang', 'kode_barang');
    }
}
