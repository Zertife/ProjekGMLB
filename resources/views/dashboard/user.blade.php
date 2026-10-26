<x-app-layout>
    <x-slot name="breadcrumb">
        <span class="current">Dashboard</span>
    </x-slot>

    <!-- Page Header -->
    <div style="margin-bottom:1.5rem;">
        <h1 class="page-title"><i class="fas fa-chart-pie" style="color:#4f46e5;margin-right:8px;"></i>Dashboard Saya</h1>
        <p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}! Berikut ringkasan aktivitas peminjaman Anda.</p>
    </div>

    <!-- Stat Cards -->
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-icon indigo"><i class="fas fa-list-check"></i></div>
            <div class="stat-info">
                <p>Total Peminjaman</p>
                <h3>{{ number_format($stats['total_loans'], 0, ',', '.') }}</h3>
                <small>Semua waktu</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon cyan"><i class="fas fa-handshake"></i></div>
            <div class="stat-info">
                <p>Sedang Dipinjam</p>
                <h3>{{ number_format($stats['active_loans'], 0, ',', '.') }}</h3>
                <small>Aktif saat ini</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-circle-check"></i></div>
            <div class="stat-info">
                <p>Sudah Dikembalikan</p>
                <h3>{{ number_format($stats['returned_loans'], 0, ',', '.') }}</h3>
                <small>Selesai</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <p>Terlambat</p>
                <h3>{{ number_format($stats['overdue_loans'], 0, ',', '.') }}</h3>
                <small>Segera kembalikan</small>
            </div>
        </div>
    </div>

    <div class="dashboard-grid user-dashboard-grid">
        <!-- Riwayat Peminjaman -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-clock-rotate-left" style="color:#4f46e5;margin-right:6px;"></i>Peminjaman Terbaru Saya</h2>
                <a href="{{ route('loans.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
            </div>
            <div class="table-wrapper">
                @if($myLoans->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Anda belum memiliki riwayat peminjaman.</p>
                        <a href="{{ route('loans.create') }}" class="btn btn-primary btn-sm" style="margin-top:0.75rem;">
                            <i class="fas fa-plus"></i> Ajukan Peminjaman
                        </a>
                    </div>
                @else
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Kategori</th>
                                <th>Qty</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myLoans as $loan)
                            <tr>
                                <td style="font-weight:500;color:#1e293b;">{{ $loan->item->name }}</td>
                                <td><span class="badge badge-secondary">{{ $loan->item->category->name }}</span></td>
                                <td>{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                <td style="font-size:0.8rem;color:#64748b;">{{ $loan->due_date->locale('id')->isoFormat('D MMM Y') }}</td>
                                <td>
                                    @if($loan->status === 'dipinjam')
                                        <span class="badge badge-info"><i class="fas fa-circle-dot"></i> Dipinjam</span>
                                    @elseif($loan->status === 'dikembalikan')
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Dikembalikan</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-clock"></i> Terlambat</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!-- Barang Tersedia -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-boxes-stacked" style="color:#10b981;margin-right:6px;"></i>Barang Tersedia</h2>
                <a href="{{ route('items.index') }}" class="btn btn-secondary btn-sm">Semua</a>
            </div>
            <div>
                @if($availableItems->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <p>Tidak ada barang tersedia</p>
                    </div>
                @else
                    @foreach($availableItems as $item)
                    <div style="padding:0.85rem 1.25rem; border-bottom:1px solid #f1f5f9; display:flex; gap:0.75rem; align-items:center;">
                        <div style="width:38px;height:38px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;font-size:0.9rem;color:#10b981;flex-shrink:0;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:0.82rem;font-weight:600;color:#1e293b;">{{ $item->name }}</p>
                            <p style="font-size:0.75rem;color:#64748b;">{{ $item->category->name }}</p>
                        </div>
                        <span class="badge badge-success">{{ number_format($item->stock, 0, ',', '.') }} unit</span>
                    </div>
                    @endforeach
                @endif
            </div>
            <div style="padding:0.85rem 1.25rem;">
                <a href="{{ route('loans.create') }}" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <i class="fas fa-plus"></i> Ajukan Peminjaman
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
