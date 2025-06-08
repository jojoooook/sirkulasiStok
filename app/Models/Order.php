<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nomor_order',
        'supplier_id', 
        'kode_barang', 
        'jumlah_order', 
        'tanggal_order', 
        'status_order', 
        'tanggal_selesai', 
        'catatan',
        'nomor_invoice'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'kode_supplier');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'kode_barang', 'kode_barang');
    }

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class, 'nomor_invoice', 'nomor_invoice');
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
