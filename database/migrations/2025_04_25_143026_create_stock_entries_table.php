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
            $table->unsignedBigInteger('item_id'); 
            $table->integer('stok_masuk'); 
            $table->timestamp('tanggal_masuk')->useCurrent(); 
            $table->string('keterangan')->nullable(); 
            $table->timestamps();  

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_entries');
    }
}
