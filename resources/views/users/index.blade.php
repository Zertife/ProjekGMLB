<x-app-layout>
    <x-slot name="breadcrumb">
        <span class="current">Manajemen User</span>
    </x-slot>

    <div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 class="page-title"><i class="fas fa-users" style="color:#2563eb;margin-right:8px;"></i>Manajemen User</h1>
            <p class="page-subtitle">Kelola akun pengguna dan hak akses sistem.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary" id="btn-tambah-user">
            <i class="fas fa-plus"></i> Tambah User
        </a>
    </div>

    <div class="card">
        <!-- Filter & Search -->
        <div class="card-header" style="flex-wrap:wrap; gap:0.75rem;">
            <h2>Daftar User <span class="badge badge-secondary" style="margin-left:6px;">{{ number_format($users->total(), 0, ',', '.') }}</span></h2>
            <form method="GET" action="{{ route('users.index') }}" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    style="width:220px;"
                    placeholder="Cari nama atau email..."
                    value="{{ request('search') }}"
                    autofocus
                >
                <select name="role" class="form-control" style="width:140px;">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-xmark"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="table-wrapper">
            @if($users->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>Tidak ada data user{{ request('search') ? ' untuk pencarian "' . request('search') . '"' : '' }}.</p>
                </div>
            @else
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Terdaftar</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td style="color:#94a3b8;">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.65rem;">
                                    <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;color:#1d4ed8;flex-shrink:0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span style="font-weight:500;color:#1e293b;">{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                        <span class="badge badge-info" style="font-size:0.65rem;">Anda</span>
                                    @endif
                                </div>
                            </td>
                            <td style="color:#64748b;">{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge badge-info"><i class="fas fa-shield-halved"></i> Admin</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-user"></i> User</span>
                                @endif
                            </td>
                            <td style="font-size:0.8rem;color:#64748b;">{{ $user->created_at->locale('id')->isoFormat('D MMM Y') }}</td>
                            <td>
                                <div style="display:flex;justify-content:center;gap:0.4rem;flex-wrap:wrap;">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm" title="Edit User">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="{{ route('users.reset-password.form', $user) }}" class="btn btn-secondary btn-sm" title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    @if($user->id !== auth()->id() && $user->loans_count === 0)
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @elseif($user->id !== auth()->id())
                                    <button class="btn btn-danger btn-sm" disabled title="Akun memiliki riwayat peminjaman dan tidak dapat dihapus.">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if($users->hasPages())
        <div style="padding:1rem 1.25rem; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
            <p style="font-size:0.8rem;color:#64748b;">
                Menampilkan {{ number_format($users->firstItem(), 0, ',', '.') }}–{{ number_format($users->lastItem(), 0, ',', '.') }} dari {{ number_format($users->total(), 0, ',', '.') }} user
            </p>
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</x-app-layout>
