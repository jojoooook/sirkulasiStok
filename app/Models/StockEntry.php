<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'nomor_nota', 'nomor_nota');
    }
    use HasFactory;

    protected $fillable = [
        'kode_barang', 
        'nomor_nota',
        'stok_masuk', 
        'tanggal_masuk', 
        'keterangan', 
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'kode_barang', 'kode_barang');  // Relasi ke model Item dengan foreign key kode_barang
    }
}
