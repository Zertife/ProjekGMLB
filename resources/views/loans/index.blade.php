<x-app-layout>
    <x-slot name="breadcrumb">
        <span class="current">Data Peminjaman</span>
    </x-slot>

    <div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 class="page-title"><i class="fas fa-handshake" style="color:#4f46e5;margin-right:8px;"></i>{{ auth()->user()->isAdmin() ? 'Manajemen Peminjaman' : 'Riwayat Peminjaman Saya' }}</h1>
            <p class="page-subtitle">{{ auth()->user()->isAdmin() ? 'Pantau dan kelola seluruh transaksi peminjaman barang inventaris.' : 'Daftar riwayat dan status peminjaman barang yang Anda ajukan.' }}</p>
        </div>
        <a href="{{ route('loans.create') }}" class="btn btn-primary" id="btn-tambah-peminjaman">
            <i class="fas fa-plus"></i> {{ auth()->user()->isAdmin() ? 'Catat Peminjaman' : 'Ajukan Peminjaman' }}
        </a>
    </div>

    <div class="card">
        <!-- Filter & Search -->
        <div class="card-header" style="flex-wrap:wrap; gap:0.75rem;">
            <h2>Daftar Transaksi <span class="badge badge-secondary" style="margin-left:6px;">{{ number_format($loans->total(), 0, ',', '.') }}</span></h2>
            <form method="GET" action="{{ route('loans.index') }}" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    style="width:200px;"
                    placeholder="{{ auth()->user()->isAdmin() ? 'Cari barang / nama user...' : 'Cari nama barang...' }}"
                    value="{{ request('search') }}"
                    autofocus
                >
                @if(auth()->user()->isAdmin())
                <select name="user_id" class="form-control" style="width:160px;">
                    <option value="">Semua Peminjam</option>
                    @foreach($usersList as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
                @endif
                <select name="status" class="form-control" style="width:150px;">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(request('search') || request('user_id') || request('status'))
                    <a href="{{ route('loans.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-xmark"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="table-wrapper">
            @if($loans->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-handshake"></i>
                    <p>Tidak ada transaksi peminjaman ditemukan.</p>
                </div>
            @else
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            @if(auth()->user()->isAdmin())
                                <th>Peminjam</th>
                            @endif
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            @if(auth()->user()->isAdmin())
                                <th style="text-align:center;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                        <tr>
                            <td style="color:#94a3b8;">{{ $loop->iteration + ($loans->currentPage() - 1) * $loans->perPage() }}</td>
                            @if(auth()->user()->isAdmin())
                                <td>
                                    <div style="font-weight:600;color:#1e293b;">{{ $loan->user->name }}</div>
                                    <small style="color:#64748b;">{{ $loan->user->email }}</small>
                                </td>
                            @endif
                            <td>
                                <div style="font-weight:600;color:#1e293b;">{{ $loan->item->name }}</div>
                                <code style="font-size:0.75rem;color:#6366f1;">{{ $loan->item->code }}</code>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ number_format($loan->quantity, 0, ',', '.') }} unit</span>
                            </td>
                            <td style="font-size:0.82rem;color:#475569;">
                                {{ $loan->loan_date ? $loan->loan_date->locale('id')->isoFormat('D MMM Y') : '-' }}
                            </td>
                            <td style="font-size:0.82rem;color:#475569;">
                                {{ $loan->due_date ? $loan->due_date->locale('id')->isoFormat('D MMM Y') : '-' }}
                            </td>
                            <td>
                                @if($loan->status === 'dipinjam')
                                    <span class="badge badge-info"><i class="fas fa-circle-dot"></i> Dipinjam</span>
                                @elseif($loan->status === 'dikembalikan')
                                    <span class="badge badge-success"><i class="fas fa-circle-check"></i> Dikembalikan</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-triangle-exclamation"></i> Terlambat</span>
                                @endif
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td style="text-align:center;">
                                    <div style="display:flex;justify-content:center;gap:0.4rem;">
                                        @if($loan->status !== 'dikembalikan')
                                            <a href="{{ route('return-logs.create', ['loan_id' => $loan->id]) }}" class="btn btn-success btn-sm" title="Proses Pengembalian">
                                                <i class="fas fa-rotate-left"></i> Kembali
                                            </a>
                                        @else
                                            <span class="badge badge-secondary" title="Selesai Dikembalikan"><i class="fas fa-check"></i> Selesai</span>
                                        @endif
                                        <form method="POST" action="{{ route('loans.destroy', $loan) }}" onsubmit="return confirm('Hapus data peminjaman #{{ $loan->id }}? Tindakan ini akan menghapus log peminjaman.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if($loans->hasPages())
        <div style="padding:1rem 1.25rem; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
            <p style="font-size:0.8rem;color:#64748b;">
                Menampilkan {{ number_format($loans->firstItem(), 0, ',', '.') }}–{{ number_format($loans->lastItem(), 0, ',', '.') }} dari {{ number_format($loans->total(), 0, ',', '.') }} data
            </p>
            {{ $loans->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</x-app-layout>
