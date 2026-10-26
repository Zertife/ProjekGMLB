<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@inventaris.sch.id',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // User 1 – Guru
        User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi@inventaris.sch.id',
            'password' => Hash::make('user123'),
            'role'     => 'user',
        ]);

        // User 2 – Siswa
        User::create([
            'name'     => 'Siti Rahayu',
            'email'    => 'siti@inventaris.sch.id',
            'password' => Hash::make('user123'),
            'role'     => 'user',
        ]);
    }
}
