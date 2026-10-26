<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $budi = User::where('email', 'budi@inventaris.sch.id')->first();
        $siti = User::where('email', 'siti@inventaris.sch.id')->first();

        $mikroskop = Item::where('code', 'LAB-001')->first();
        $proyektor = Item::where('code', 'ELK-001')->first();
        $bolaBsk   = Item::where('code', 'OLR-001')->first();
        $laptop    = Item::where('code', 'ELK-002')->first();
        $gelasUkur = Item::where('code', 'LAB-002')->first();

        $loans = [
            // Loan 1 – sudah dikembalikan
            [
                'user_id'   => $budi->id,
                'item_id'   => $mikroskop->id,
                'quantity'  => 2,
                'loan_date' => '2026-09-01',
                'due_date'  => '2026-09-08',
                'status'    => 'dikembalikan',
            ],
            // Loan 2 – sudah dikembalikan
            [
                'user_id'   => $siti->id,
                'item_id'   => $proyektor->id,
                'quantity'  => 1,
                'loan_date' => '2026-09-10',
                'due_date'  => '2026-09-17',
                'status'    => 'dikembalikan',
            ],
            // Loan 3 – sudah dikembalikan
            [
                'user_id'   => $budi->id,
                'item_id'   => $bolaBsk->id,
                'quantity'  => 3,
                'loan_date' => '2026-09-15',
                'due_date'  => '2026-09-22',
                'status'    => 'dikembalikan',
            ],
            // Loan 4 – sedang dipinjam
            [
                'user_id'   => $siti->id,
                'item_id'   => $laptop->id,
                'quantity'  => 2,
                'loan_date' => '2026-10-01',
                'due_date'  => '2026-10-15',
                'status'    => 'dipinjam',
            ],
            // Loan 5 – terlambat
            [
                'user_id'   => $budi->id,
                'item_id'   => $gelasUkur->id,
                'quantity'  => 5,
                'loan_date' => '2026-09-20',
                'due_date'  => '2026-09-27',
                'status'    => 'terlambat',
            ],
        ];

        foreach ($loans as $loan) {
            Loan::create($loan);
        }
    }
}
