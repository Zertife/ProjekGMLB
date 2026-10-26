<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Http\Requests\StoreReturnLogRequest;
use App\Http\Requests\ReturnLogIndexRequest;
use App\Http\Requests\CreateReturnLogRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnLogController extends Controller
{
    public function index(ReturnLogIndexRequest $request)
    {
        $filters = $request->validated();
        $query = ReturnLog::with(['loan' => fn ($query) => $query->withTrashed()->with(['user', 'item'])]);

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->whereHas('loan', function ($loanQuery) use ($search) {
                $loanQuery->withTrashed()->where(function ($query) use ($search) {
                    $query->whereHas('item', fn ($itemQuery) => $itemQuery->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            });
        }

        if (! empty($filters['condition'])) {
            $query->where('condition_on_return', $filters['condition']);
        }

        $returnLogs = $query->latest('returned_at')->paginate(10)->withQueryString();

        return view('return-logs.index', compact('returnLogs'));
    }

    public function create(CreateReturnLogRequest $request)
    {
        $loans = Loan::with(['user', 'item'])
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereDoesntHave('returnLog')
            ->orderBy('due_date')
            ->get();

        return view('return-logs.create', [
            'loans' => $loans,
            'selectedLoanId' => $request->query('loan_id'),
        ]);
    }

    public function store(StoreReturnLogRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $loan = Loan::with('item')->lockForUpdate()->findOrFail($validated['loan_id']);

            if (! in_array($loan->status, ['dipinjam', 'terlambat'], true) || $loan->returnLog()->exists()) {
                throw ValidationException::withMessages([
                    'loan_id' => 'Peminjaman ini sudah selesai dikembalikan atau tidak lagi aktif.',
                ]);
            }

            if ($validated['returned_at'] < $loan->loan_date->toDateString()) {
                throw ValidationException::withMessages([
                    'returned_at' => 'Tanggal pengembalian tidak boleh sebelum tanggal peminjaman.',
                ]);
            }

            ReturnLog::create([
                ...$validated,
                'notes' => $validated['notes'] ?? null,
            ]);

            $loan->update(['status' => 'dikembalikan']);

            // Hanya barang dengan kondisi layak pakai yang kembali ke stok tersedia.
            if ($validated['condition_on_return'] === 'baik' && $loan->item) {
                $loan->item->increment('stock', $loan->quantity);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'return_loan',
                'description' => "Mencatat pengembalian peminjaman #{$loan->id} ({$loan->item?->name}) dengan kondisi {$validated['condition_on_return']}.",
            ]);
        });

        return redirect()->route('return-logs.index')->with('success', 'Pengembalian berhasil dicatat dan status peminjaman diperbarui.');
    }
}
