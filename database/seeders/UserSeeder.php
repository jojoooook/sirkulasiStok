<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'karyawan1',
                'name' => 'Karyawan One',
                'email' => 'karyawan1@example.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
