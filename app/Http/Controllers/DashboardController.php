<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard sesuai role user.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->userDashboard();
    }

    /**
     * Dashboard Admin: ringkasan statistik keseluruhan sistem.
     */
    private function adminDashboard()
    {
        $loanStats = Loan::query()
            ->selectRaw(
                'COUNT(*) as total_loans,
                COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) as active_loans,
                COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) as overdue_loans',
                ['dipinjam', 'terlambat'],
            )
            ->first();

        $stats = [
            'total_users'       => User::where('role', 'user')->count(),
            'total_categories'  => Category::count(),
            'total_items'       => Item::count(),
            'total_loans'       => (int) $loanStats->total_loans,
            'active_loans'      => (int) $loanStats->active_loans,
            'overdue_loans'     => (int) $loanStats->overdue_loans,
        ];

        $recentLoans = Loan::with(['user', 'item'])
            ->latest()
            ->take(8)
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        $lowStockItems = Item::where('stock', '<=', 3)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentLoans', 'recentActivities', 'lowStockItems'));
    }

    /**
     * Dashboard User: ringkasan aktivitas peminjaman user sendiri.
     */
    private function userDashboard()
    {
        $user = Auth::user();
        $loanStats = $user->loans()
            ->selectRaw(
                'COUNT(*) as total_loans,
                COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) as active_loans,
                COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) as returned_loans,
                COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) as overdue_loans',
                ['dipinjam', 'dikembalikan', 'terlambat'],
            )
            ->first();

        $stats = [
            'total_loans'    => (int) $loanStats->total_loans,
            'active_loans'   => (int) $loanStats->active_loans,
            'returned_loans' => (int) $loanStats->returned_loans,
            'overdue_loans'  => (int) $loanStats->overdue_loans,
        ];

        $myLoans = $user->loans()
            ->with(['item.category'])
            ->latest()
            ->take(8)
            ->get();

        $availableItems = Item::where('stock', '>', 0)
            ->where('condition', '!=', 'rusak berat')
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.user', compact('stats', 'myLoans', 'availableItems'));
    }
}
