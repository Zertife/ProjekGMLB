<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
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
        $stats = [
            'total_users'       => User::where('role', 'user')->count(),
            'total_categories'  => Category::count(),
            'total_items'       => Item::count(),
            'total_loans'       => Loan::count(),
            'active_loans'      => Loan::where('status', 'dipinjam')->count(),
            'overdue_loans'     => Loan::where('status', 'terlambat')->count(),
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

        $stats = [
            'total_loans'    => $user->loans()->count(),
            'active_loans'   => $user->loans()->where('status', 'dipinjam')->count(),
            'returned_loans' => $user->loans()->where('status', 'dikembalikan')->count(),
            'overdue_loans'  => $user->loans()->where('status', 'terlambat')->count(),
        ];

        $myLoans = $user->loans()
            ->with(['item.category'])
            ->latest()
            ->take(8)
            ->get();

        $availableItems = Item::where('stock', '>', 0)
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.user', compact('stats', 'myLoans', 'availableItems'));
    }
}
