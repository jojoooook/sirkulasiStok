<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockExitsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_exits', function (Blueprint $table) {
            $table->id();  
            $table->string('kode_barang');  
            $table->integer('stok_keluar');  
            $table->timestamp('tanggal_keluar')->useCurrent();  
            $table->string('keterangan')->nullable();  
            $table->timestamps();  

            $table->foreign('kode_barang')->references('kode_barang')->on('items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_exits');
    }
}
