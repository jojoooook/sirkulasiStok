<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'nomor_nota', 'nomor_nota');
    }
    protected $fillable = [
        'supplier_id', 
        'kode_barang', 
        'nomor_nota',
        'jumlah_order', 
        'tanggal_order', 
        'status_order', 
        'tanggal_selesai', 
        'catatan'
    ];

    public function supplier()
{
    return $this->belongsTo(Supplier::class, 'supplier_id', 'kode_supplier');
}


    public function item()
    {
        return $this->belongsTo(Item::class, 'kode_barang', 'kode_barang');
    }

    public function scopeOrderBySupplierName($query, $direction = 'asc')
    {
        return $query->leftJoin('suppliers', 'orders.supplier_id', '=', 'suppliers.kode_supplier')
                     ->orderBy('suppliers.nama', $direction)
                     ->select('orders.*');
    }

    public function scopeOrderByItemName($query, $direction = 'asc')
    {
        return $query->leftJoin('items', 'orders.kode_barang', '=', 'items.kode_barang')
                     ->orderBy('items.nama_barang', $direction)
                     ->select('orders.*');
    }
}
