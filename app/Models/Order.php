<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'supplier_id', 
        'item_id', 
        'jumlah_order', 
        'tanggal_order', 
        'status_order', 
        'tanggal_selesai', 
        'catatan'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
