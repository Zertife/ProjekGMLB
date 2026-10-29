<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\ReturnLog;
use Illuminate\Database\Seeder;

class ReturnLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil 3 loan yang berstatus 'dikembalikan'
        $returnedLoans = Loan::where('status', 'dikembalikan')->get();

        $returnData = [
            [
                'returned_at'        => '2026-09-07',
                'condition_on_return' => 'baik',
                'notes'              => 'Dikembalikan tepat waktu, kondisi baik.',
            ],
            [
                'returned_at'        => '2026-09-16',
                'condition_on_return' => 'baik',
                'notes'              => 'Proyektor dikembalikan dalam kondisi normal.',
            ],
            [
                'returned_at'        => '2026-09-21',
                'condition_on_return' => 'rusak ringan',
                'notes'              => 'Terdapat sedikit goresan pada bola basket.',
            ],
        ];

        foreach ($returnedLoans as $index => $loan) {
            ReturnLog::create([
                'loan_id'            => $loan->id,
                'returned_at'        => $returnData[$index]['returned_at'],
                'condition_on_return' => $returnData[$index]['condition_on_return'],
                'notes'              => $returnData[$index]['notes'],
            ]);

            if ($returnData[$index]['condition_on_return'] !== 'baik') {
                $loan->item?->decrement('stock', $loan->quantity);
            }
        }
    }
}
