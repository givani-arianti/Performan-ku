<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin HR',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Create Manajer
        User::updateOrCreate(
            ['email' => 'manajer@gmail.com'],
            [
                'name' => 'Bapak Manajer',
                'password' => Hash::make('password123'),
                'role' => 'manajer',
                'divisi' => 'IT & Software',
            ]
        );

        // Create Karyawan
        User::updateOrCreate(
            ['email' => 'karyawan@gmail.com'],
            [
                'name' => 'Pegawai Satu',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'divisi' => 'IT & Software',
            ]
        );
        
        User::updateOrCreate(
            ['email' => 'karyawan2@gmail.com'],
            [
                'name' => 'Pegawai Dua',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'divisi' => 'Marketing & Sales',
            ]
        );
    }
}
