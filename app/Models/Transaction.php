<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $primaryKey = 'nomor_nota';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nomor_nota',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'nomor_nota', 'nomor_nota');
    }

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class, 'nomor_nota', 'nomor_nota');
    }

    public function stockExits()
    {
        return $this->hasMany(StockExit::class, 'nomor_nota', 'nomor_nota');
    }
}
