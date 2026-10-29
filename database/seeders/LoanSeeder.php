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
        $user1 = User::where('email', 'user1@email.com')->firstOrFail();
        $user2 = User::where('email', 'user2@email.com')->firstOrFail();

        $mikroskop = Item::where('code', 'LAB-001')->firstOrFail();
        $proyektor = Item::where('code', 'ELK-001')->firstOrFail();
        $bolaBsk   = Item::where('code', 'OLR-001')->firstOrFail();
        $laptop    = Item::where('code', 'ELK-002')->firstOrFail();
        $gelasUkur = Item::where('code', 'LAB-002')->firstOrFail();

        $loans = [
            // Loan 1 – sudah dikembalikan
            [
                'user_id'   => $user1->id,
                'item_id'   => $mikroskop->id,
                'quantity'  => 2,
                'loan_date' => '2026-09-01',
                'due_date'  => '2026-09-08',
                'status'    => 'dikembalikan',
            ],
            // Loan 2 – sudah dikembalikan
            [
                'user_id'   => $user2->id,
                'item_id'   => $proyektor->id,
                'quantity'  => 1,
                'loan_date' => '2026-09-10',
                'due_date'  => '2026-09-17',
                'status'    => 'dikembalikan',
            ],
            // Loan 3 – sudah dikembalikan
            [
                'user_id'   => $user1->id,
                'item_id'   => $bolaBsk->id,
                'quantity'  => 3,
                'loan_date' => '2026-09-15',
                'due_date'  => '2026-09-22',
                'status'    => 'dikembalikan',
            ],
            // Loan 4 – sedang dipinjam
            [
                'user_id'   => $user2->id,
                'item_id'   => $laptop->id,
                'quantity'  => 2,
                'loan_date' => '2026-10-01',
                'due_date'  => '2026-10-15',
                'status'    => 'dipinjam',
            ],
            // Loan 5 – terlambat
            [
                'user_id'   => $user1->id,
                'item_id'   => $gelasUkur->id,
                'quantity'  => 5,
                'loan_date' => '2026-09-20',
                'due_date'  => '2026-09-27',
                'status'    => 'terlambat',
            ],
        ];

        foreach ($loans as $loan) {
            $createdLoan = Loan::create($loan);

            if (in_array($createdLoan->status, ['dipinjam', 'terlambat'], true)) {
                $createdLoan->item->decrement('stock', $createdLoan->quantity);
            }
        }
    }
}
