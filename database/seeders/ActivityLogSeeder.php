<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user1 = User::where('email', 'user1@email.com')->firstOrFail();
        $user2 = User::where('email', 'user2@email.com')->firstOrFail();

        $logs = [
            [
                'user_id'     => $admin->id,
                'action'      => 'create_category',
                'description' => 'Admin menambahkan kategori baru: Alat Laboratorium.',
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'create_category',
                'description' => 'Admin menambahkan kategori baru: Alat Olahraga.',
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'create_item',
                'description' => 'Admin menambahkan barang baru: Mikroskop (LAB-001).',
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'create_item',
                'description' => 'Admin menambahkan barang baru: Proyektor Epson (ELK-001).',
            ],
            [
                'user_id'     => $user1->id,
                'action'      => 'create_loan',
                'description' => 'User 1 mengajukan peminjaman Mikroskop sebanyak 2 unit.',
            ],
            [
                'user_id'     => $user2->id,
                'action'      => 'create_loan',
                'description' => 'User 2 mengajukan peminjaman Proyektor Epson sebanyak 1 unit.',
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'verify_loan',
                'description' => 'Admin memverifikasi peminjaman Mikroskop oleh User 1.',
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'verify_return',
                'description' => 'Admin memverifikasi pengembalian Proyektor Epson oleh User 2.',
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create($log);
        }
    }
}
