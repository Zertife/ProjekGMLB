<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\LoanIndexRequest;
use App\Http\Requests\CreateLoanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    /**
     * Tampilkan daftar peminjaman.
     * Admin: melihat semua peminjaman dengan filter.
     * User: hanya melihat peminjaman miliknya.
     */
    public function index(LoanIndexRequest $request)
    {
        $filters = $request->validated();
        $user = Auth::user();
        $query = Loan::with(['user', 'item.category', 'returnLog']);

        if (!$user->isAdmin()) {
            // User hanya melihat peminjaman miliknya
            $query->where('user_id', $user->id);
        } else {
            // Filter Admin
            if (! empty($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($query) use ($search, $user) {
                $query->whereHas('item', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
                if ($user->isAdmin()) {
                    $query->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        // Cek dan update otomatis status terlambat jika sudah lewat due_date dan masih 'dipinjam'
        Loan::where('status', 'dipinjam')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'terlambat']);

        $loans = $query->latest()->paginate(10)->withQueryString();
        $usersList = $user->isAdmin() ? User::where('role', 'user')->orderBy('name')->get() : collect();

        return view('loans.index', compact('loans', 'usersList'));
    }

    /**
     * Form tambah / ajukan peminjaman.
     */
    public function create(CreateLoanRequest $request)
    {
        $user = Auth::user();
        $items = Item::where('stock', '>', 0)
            ->where('condition', '!=', 'rusak berat')
            ->orderBy('name')
            ->get();
        $users = $user->isAdmin() ? User::where('role', 'user')->orderBy('name')->get() : collect();
        $selectedItemId = $request->query('item_id');

        return view('loans.create', compact('items', 'users', 'selectedItemId'));
    }

    /**
     * Simpan peminjaman baru & kurangi stok barang.
     */
    public function store(StoreLoanRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $borrowerId = $user->isAdmin() ? $validated['user_id'] : $user->id;

        // Gunakan Database Transaction agar aman dan atomic saat update stok
        DB::transaction(function () use ($validated, $borrowerId, $user) {
            // Lock row item untuk mencegah race condition pengurangan stok
            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);

            if ($item->stock < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok barang \"{$item->name}\" tidak mencukupi. Sisa stok: ".number_format($item->stock, 0, ',', '.')." unit.",
                ]);
            }

            if ($item->condition === 'rusak berat') {
                throw ValidationException::withMessages([
                    'item_id' => "Barang \"{$item->name}\" tidak dapat dipinjam karena kondisinya rusak berat.",
                ]);
            }

            // Kurangi stok barang
            $item->decrement('stock', $validated['quantity']);

            // Buat record peminjaman
            $newLoan = Loan::create([
                'user_id'   => $borrowerId,
                'item_id'   => $item->id,
                'quantity'  => $validated['quantity'],
                'loan_date' => $validated['loan_date'],
                'due_date'  => $validated['due_date'],
                'status'    => 'dipinjam',
            ]);

            $borrower = User::find($borrowerId);
            $actionText = $user->isAdmin()
                ? "Admin mencatat peminjaman barang {$item->name} ({$validated['quantity']} unit) untuk {$borrower->name}"
                : "User {$borrower->name} mengajukan peminjaman barang {$item->name} ({$validated['quantity']} unit)";

            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'create_loan',
                'description' => $actionText,
            ]);

        });

        return redirect()->route('loans.index')
            ->with('success', 'Peminjaman berhasil dicatat dan stok barang telah diperbarui.');
    }

    /**
     * Hapus riwayat peminjaman (Soft Delete, Admin only).
     */
    public function destroy(Loan $loan)
    {
        DB::transaction(function () use ($loan) {
            $loan = Loan::with('item')->lockForUpdate()->findOrFail($loan->id);

            if (in_array($loan->status, ['dipinjam', 'terlambat'], true) && $loan->item) {
                $loan->item->increment('stock', $loan->quantity);
            }

            $loan->delete();

            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'delete_loan',
                'description' => "Menghapus peminjaman ID #{$loan->id} (Soft Delete)",
            ]);
        });

        return redirect()->route('loans.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
