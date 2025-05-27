<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $primaryKey = 'kode_barang';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'stok',
        'harga', 
        'gambar',
        'supplier_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withDefault();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
