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
            'email'    => 'admin@email.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // User 1
        User::create([
            'name'     => 'User 1',
            'email'    => 'user1@email.com',
            'password' => Hash::make('user1234'),
            'role'     => 'user',
        ]);

        // User 2
        User::create([
            'name'     => 'User 2',
            'email'    => 'user2@email.com',
            'password' => Hash::make('user123!'),
            'role'     => 'user',
        ]);
    }

}
