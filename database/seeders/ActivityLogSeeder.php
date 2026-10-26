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
        $budi  = User::where('email', 'budi@inventaris.sch.id')->first();
        $siti  = User::where('email', 'siti@inventaris.sch.id')->first();

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
                'user_id'     => $budi->id,
                'action'      => 'create_loan',
                'description' => 'Budi Santoso mengajukan peminjaman Mikroskop sebanyak 2 unit.',
            ],
            [
                'user_id'     => $siti->id,
                'action'      => 'create_loan',
                'description' => 'Siti Rahayu mengajukan peminjaman Proyektor Epson sebanyak 1 unit.',
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'verify_loan',
                'description' => 'Admin memverifikasi peminjaman Mikroskop oleh Budi Santoso.',
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'verify_return',
                'description' => 'Admin memverifikasi pengembalian Proyektor Epson oleh Siti Rahayu.',
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create($log);
        }
    }
}
