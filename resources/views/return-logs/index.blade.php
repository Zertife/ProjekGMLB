<x-app-layout>
    <x-slot name="breadcrumb">
        <a href="{{ route('loans.index') }}">Peminjaman</a>
        <span class="separator">/</span>
        <span class="current">Pengembalian</span>
    </x-slot>

    <div class="page-heading">
        <div>
            <h1 class="page-title">Pengembalian Barang</h1>
            <p class="page-subtitle">Catatan kondisi dan tanggal pengembalian barang inventaris.</p>
        </div>
        <a href="{{ route('return-logs.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat Pengembalian</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Riwayat Pengembalian <span class="badge badge-secondary">{{ number_format($returnLogs->total(), 0, ',', '.') }}</span></h2>
            <form method="GET" action="{{ route('return-logs.index') }}" class="filter-form">
                <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Cari barang atau peminjam..." autofocus>
                <select class="form-control" name="condition" aria-label="Filter kondisi">
                    <option value="">Semua kondisi</option>
                    @foreach(['baik' => 'Baik', 'rusak ringan' => 'Rusak ringan', 'rusak berat' => 'Rusak berat'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('condition') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-search"></i> Cari</button>
                @if(request()->filled('search') || request()->filled('condition'))
                    <a class="btn btn-secondary btn-sm" href="{{ route('return-logs.index') }}">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-wrapper">
            @if($returnLogs->isEmpty())
                <div class="empty-state"><i class="fas fa-rotate-left"></i><p>Belum ada catatan pengembalian.</p></div>
            @else
                <table class="inv-table">
                    <thead><tr><th>Tanggal</th><th>Peminjam</th><th>Barang</th><th>Jumlah</th><th>Kondisi</th><th>Catatan</th></tr></thead>
                    <tbody>
                        @foreach($returnLogs as $returnLog)
                            <tr>
                                <td>{{ $returnLog->returned_at->locale('id')->isoFormat('D MMM Y') }}</td>
                                <td>{{ $returnLog->loan?->user?->name ?? 'Akun tidak tersedia' }}</td>
                                <td>{{ $returnLog->loan?->item?->name ?? 'Barang tidak tersedia' }}</td>
                                <td>{{ number_format($returnLog->loan?->quantity ?? 0, 0, ',', '.') }} unit</td>
                                <td><span class="badge {{ $returnLog->condition_on_return === 'baik' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($returnLog->condition_on_return) }}</span></td>
                                <td>{{ $returnLog->notes ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        @if($returnLogs->hasPages())
            <div class="pagination-footer"><p>Menampilkan {{ number_format($returnLogs->firstItem(), 0, ',', '.') }}–{{ number_format($returnLogs->lastItem(), 0, ',', '.') }} dari {{ number_format($returnLogs->total(), 0, ',', '.') }} data</p>{{ $returnLogs->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
