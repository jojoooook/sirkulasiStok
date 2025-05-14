<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id');
            $table->string('kode_barang');
            $table->integer('jumlah_order');
            $table->timestamp('tanggal_order')->useCurrent();
            $table->enum('status_order', ['pending', 'dibatalkan', 'selesai'])->default('pending');
            $table->timestamp('tanggal_selesai')->nullable(); 
            $table->text('catatan')->nullable();
            $table->string('nomor_nota', 50)->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('kode_supplier')->on('suppliers')->onDelete('cascade');
            $table->foreign('kode_barang')->references('kode_barang')->on('items')->onDelete('cascade');
            $table->foreign('nomor_nota')->references('nomor_nota')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
