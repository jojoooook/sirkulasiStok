<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockExit extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'stok_keluar',
        'tanggal_keluar',
        'keterangan',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
