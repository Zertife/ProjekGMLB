<x-app-layout>
    <x-slot name="breadcrumb">
        <span class="current">Dashboard Admin</span>
    </x-slot>

    <!-- Page Header -->
    <div style="margin-bottom:1.5rem; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 class="page-title"><i class="fas fa-chart-pie" style="color:#4f46e5;margin-right:8px;"></i>Dashboard Admin</h1>
            <p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}! Berikut ringkasan sistem inventaris.</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-icon indigo"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <p>Total User</p>
                <h3>{{ number_format($stats['total_users'], 0, ',', '.') }}</h3>
                <small>Pengguna terdaftar</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon cyan"><i class="fas fa-tags"></i></div>
            <div class="stat-info">
                <p>Kategori</p>
                <h3>{{ number_format($stats['total_categories'], 0, ',', '.') }}</h3>
                <small>Kategori barang</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon violet"><i class="fas fa-boxes-stacked"></i></div>
            <div class="stat-info">
                <p>Total Barang</p>
                <h3>{{ number_format($stats['total_items'], 0, ',', '.') }}</h3>
                <small>Item inventaris</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-handshake"></i></div>
            <div class="stat-info">
                <p>Sedang Dipinjam</p>
                <h3>{{ number_format($stats['active_loans'], 0, ',', '.') }}</h3>
                <small>Peminjaman aktif</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <p>Terlambat</p>
                <h3>{{ number_format($stats['overdue_loans'], 0, ',', '.') }}</h3>
                <small>Melewati jatuh tempo</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-triangle-exclamation"></i></div>
            <div class="stat-info">
                <p>Stok Kritis</p>
                <h3>{{ $lowStockItems->count() }}</h3>
                <small>Barang stok ≤ 3</small>
            </div>
        </div>
    </div>

    <div class="dashboard-grid admin-dashboard-grid">
        <!-- Recent Loans -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-handshake" style="color:#10b981;margin-right:6px;"></i>Peminjaman Terbaru</h2>
                <a href="{{ route('loans.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
            </div>
            <div class="table-wrapper">
                @if($recentLoans->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada data peminjaman</p>
                    </div>
                @else
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Barang</th>
                                <th>Status</th>
                                <th>Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLoans as $loan)
                            <tr>
                                <td>
                                    <div style="font-weight:500;color:#1e293b;">{{ $loan->user->name }}</div>
                                </td>
                                <td>
                                    <div style="color:#64748b;">{{ $loan->item->name }}</div>
                                    <small style="color:#94a3b8;">Jumlah: {{ number_format($loan->quantity, 0, ',', '.') }}</small>
                                </td>
                                <td>
                                    @if($loan->status === 'dipinjam')
                                        <span class="badge badge-info"><i class="fas fa-circle-dot"></i> Dipinjam</span>
                                    @elseif($loan->status === 'dikembalikan')
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Dikembalikan</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-clock"></i> Terlambat</span>
                                    @endif
                                </td>
                                <td style="font-size:0.8rem;color:#64748b;">
                                    {{ $loan->due_date->locale('id')->isoFormat('D MMM Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-scroll" style="color:#7c3aed;margin-right:6px;"></i>Aktivitas Terbaru</h2>
                <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
            </div>
            <div style="padding:0;">
                @if($recentActivities->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-scroll"></i>
                        <p>Belum ada aktivitas</p>
                    </div>
                @else
                    @foreach($recentActivities as $activity)
                    <div style="padding:0.85rem 1.25rem; border-bottom:1px solid #f1f5f9; display:flex; gap:0.75rem; align-items:flex-start;">
                        <div style="width:32px;height:32px;border-radius:8px;background:#ede9fe;display:flex;align-items:center;justify-content:center;font-size:0.75rem;color:#7c3aed;flex-shrink:0;">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:0.82rem;font-weight:500;color:#1e293b;">{{ $activity->user?->name ?? 'Akun telah dihapus' }}</p>
                            <p style="font-size:0.78rem;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $activity->description }}</p>
                            <p style="font-size:0.72rem;color:#94a3b8;margin-top:2px;">{{ $activity->created_at->locale('id')->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Low Stock Warning -->
    @if($lowStockItems->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-triangle-exclamation" style="color:#f59e0b;margin-right:6px;"></i>Peringatan Stok Kritis</h2>
            <a href="{{ route('items.index') }}" class="btn btn-secondary btn-sm">Kelola Barang</a>
        </div>
        <div class="table-wrapper">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockItems as $item)
                    <tr>
                        <td><code style="background:#f8fafc;padding:2px 6px;border-radius:4px;font-size:0.78rem;">{{ $item->code }}</code></td>
                        <td style="font-weight:500;">{{ $item->name }}</td>
                        <td><span class="badge badge-secondary">{{ $item->category->name }}</span></td>
                        <td>
                            <span class="badge {{ $item->stock == 0 ? 'badge-danger' : 'badge-warning' }}">
                                {{ number_format($item->stock, 0, ',', '.') }} unit
                            </span>
                        </td>
                        <td>
                            @if($item->condition === 'baik')
                                <span class="badge badge-success">Baik</span>
                            @elseif($item->condition === 'rusak ringan')
                                <span class="badge badge-warning">Rusak Ringan</span>
                            @else
                                <span class="badge badge-danger">Rusak Berat</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</x-app-layout>
