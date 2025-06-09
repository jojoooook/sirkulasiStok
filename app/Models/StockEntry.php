<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'kode_barang', 
        'nomor_invoice',
        'stok_masuk', 
        'tanggal_masuk', 
        'keterangan', 
        'order_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'kode_barang', 'kode_barang');  // Relasi ke model Item dengan foreign key kode_barang
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');  // Relasi ke model Order dengan foreign key order_id
    }
}
