<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->string('kode_barang')->primary();
            $table->string('nama_barang');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->integer('stok');
            $table->double('harga', 10, 2);
            $table->string('gambar')->nullable();
            $table->string('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('kode_supplier')->on('suppliers')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}