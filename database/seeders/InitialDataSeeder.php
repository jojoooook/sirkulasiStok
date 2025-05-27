<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Removed categories insertion

        // Insert 2 suppliers
        DB::table('suppliers')->insert([
            [
                'kode_supplier' => 'SUP001',
                'nama' => 'Supplier A',
                'alamat' => 'Jl. Merdeka No.1',
                'telepon' => '081234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_supplier' => 'SUP002',
                'nama' => 'Supplier B',
                'alamat' => 'Jl. Sudirman No.2',
                'telepon' => '089876543210',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert 2 items without category references
        DB::table('items')->insert([
            [
                'kode_barang' => 'BRG001',
                'nama_barang' => 'TV LED 32 Inch',
                'stok' => 10,
                'harga' => 2500000,
                'supplier_id' => 'SUP001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_barang' => 'BRG002',
                'nama_barang' => 'Kipas Angin',
                'stok' => 15,
                'harga' => 350000,
                'supplier_id' => 'SUP002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
