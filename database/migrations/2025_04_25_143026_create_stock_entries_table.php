<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();  
            $table->string('kode_barang'); 
            $table->string('nomor_nota', 50)->nullable();
            $table->integer('stok_masuk'); 
            $table->timestamp('tanggal_masuk')->useCurrent(); 
            $table->string('keterangan')->nullable(); 
            $table->timestamps();  

            $table->foreign('kode_barang')->references('kode_barang')->on('items')->onDelete('cascade');
            $table->foreign('nomor_nota')->references('nomor_nota')->on('transactions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_entries');
    }
}
