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
            $table->string('supplier_id');
            $table->string('nomor_invoice')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string('kode_barang');
            $table->integer('stok_masuk'); 
            $table->timestamp('tanggal_masuk')->useCurrent(); 
            $table->string('keterangan')->nullable(); 
            $table->timestamps();  

            $table->foreign('supplier_id')->references('kode_supplier')->on('suppliers')->onDelete('cascade');
            $table->foreign('kode_barang')->references('kode_barang')->on('items')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_entries');
    }
}
