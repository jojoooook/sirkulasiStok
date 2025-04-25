<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 
        'stok_masuk', 
        'tanggal_masuk', 
        'keterangan', 
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);  // Relasi ke model Item
    }
}
