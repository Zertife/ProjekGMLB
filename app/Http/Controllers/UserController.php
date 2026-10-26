<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Http\Requests\ResetUserPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserIndexRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Daftar semua user dengan search & pagination.
     */
    public function index(UserIndexRequest $request)
    {
        $filters = $request->validated();
        $query = User::withCount(['loans' => fn ($loans) => $loans->withTrashed()]);

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        if (! empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Form tambah user baru.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Simpan user baru.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'create_user',
            'description' => 'Menambahkan akun user baru: ' . $user->name . ' (' . $user->email . ') dengan role ' . $user->role,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User "' . $user->name . '" berhasil ditambahkan.');
    }

    /**
     * Form edit user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update data user.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $oldRole = $user->role;
        $user->update($validated);

        $description = 'Memperbarui akun user: ' . $user->name . ' (' . $user->email . ')';
        if ($oldRole !== $validated['role']) {
            $description .= ' — role diubah dari ' . $oldRole . ' ke ' . $validated['role'];
        }

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'update_user',
            'description' => $description,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User "' . $user->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus user (tidak bisa hapus diri sendiri).
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->loans()->withTrashed()->exists()) {
            return redirect()->route('users.index')
                ->with('error', 'Akun tidak dapat dihapus karena memiliki riwayat peminjaman.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'delete_user',
            'description' => 'Menghapus akun user: ' . $name,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User "' . $name . '" berhasil dihapus.');
    }

    /**
     * Form reset password user.
     */
    public function resetPasswordForm(User $user)
    {
        return view('users.reset-password', compact('user'));
    }

    /**
     * Proses reset password user.
     */
    public function resetPassword(ResetUserPasswordRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'reset_password',
            'description' => 'Mereset password akun user: ' . $user->name . ' (' . $user->email . ')',
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Password user "' . $user->name . '" berhasil direset.');
    }
}
