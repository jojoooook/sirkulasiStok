<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'kode_supplier';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_supplier',
        'nama',
        'alamat',
        'telepon',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'supplier_id', 'kode_supplier');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'supplier_id', 'kode_supplier');
    }
}
