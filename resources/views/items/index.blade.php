<x-app-layout>
    <x-slot name="breadcrumb">
        <span class="current">Daftar Barang</span>
    </x-slot>

    <div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 class="page-title"><i class="fas fa-boxes-stacked" style="color:#4f46e5;margin-right:8px;"></i>Daftar Barang Inventaris</h1>
            <p class="page-subtitle">Kelola dan pantau seluruh ketersediaan barang inventaris sekolah.</p>
        </div>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('items.create') }}" class="btn btn-primary" id="btn-tambah-barang">
                <i class="fas fa-plus"></i> Tambah Barang
            </a>
        @else
            <a href="{{ route('loans.create') }}" class="btn btn-primary" id="btn-ajukan-peminjaman">
                <i class="fas fa-handshake"></i> Ajukan Peminjaman
            </a>
        @endif
    </div>

    <div class="card">
        <!-- Filter and Search -->
        <div class="card-header" style="flex-wrap:wrap; gap:0.75rem;">
            <h2>Data Barang <span class="badge badge-secondary" style="margin-left:6px;">{{ number_format($items->total(), 0, ',', '.') }}</span></h2>
            <form method="GET" action="{{ route('items.index') }}" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    style="width:200px;"
                    placeholder="Cari kode atau nama..."
                    value="{{ request('search') }}"
                    autofocus
                >
                <select name="category_id" class="form-control" style="width:160px;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <select name="condition" class="form-control" style="width:140px;">
                    <option value="">Semua Kondisi</option>
                    <option value="baik" {{ request('condition') === 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak ringan" {{ request('condition') === 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak berat" {{ request('condition') === 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(request('search') || request('category_id') || request('condition'))
                    <a href="{{ route('items.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-xmark"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="table-wrapper">
            @if($items->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-boxes-stacked"></i>
                    <p>Tidak ada data barang yang ditemukan.</p>
                </div>
            @else
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Kondisi</th>
                            @if(auth()->user()->isAdmin())
                                <th style="text-align:center;">Aksi</th>
                            @else
                                <th style="text-align:center;">Peminjaman</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td style="color:#94a3b8;">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                            <td>
                                <code style="background:#f1f5f9;color:#4f46e5;font-weight:600;padding:2px 6px;border-radius:4px;font-size:0.8rem;">
                                    {{ $item->code }}
                                </code>
                            </td>
                            <td>
                                <div style="font-weight:600;color:#1e293b;">{{ $item->name }}</div>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $item->category->name }}</span>
                            </td>
                            <td>
                                @if($item->stock > 5)
                                    <span class="badge badge-success">{{ number_format($item->stock, 0, ',', '.') }} unit</span>
                                @elseif($item->stock > 0)
                                    <span class="badge badge-warning">{{ number_format($item->stock, 0, ',', '.') }} unit</span>
                                @else
                                    <span class="badge badge-danger">Habis (0)</span>
                                @endif
                            </td>
                            <td>
                                @if($item->condition === 'baik')
                                    <span class="badge badge-success"><i class="fas fa-circle-check"></i> Baik</span>
                                @elseif($item->condition === 'rusak ringan')
                                    <span class="badge badge-warning"><i class="fas fa-triangle-exclamation"></i> Rusak Ringan</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-circle-xmark"></i> Rusak Berat</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if(auth()->user()->isAdmin())
                                    <div style="display:flex;justify-content:center;gap:0.4rem;">
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit Barang">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('Yakin ingin menghapus barang {{ addslashes($item->name) }} (Soft Delete)?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Barang">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    @if($item->stock > 0)
                                        <a href="{{ route('loans.create', ['item_id' => $item->id]) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-handshake"></i> Pinjam
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            Stok Habis
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if($items->hasPages())
        <div style="padding:1rem 1.25rem; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
            <p style="font-size:0.8rem;color:#64748b;">
                Menampilkan {{ number_format($items->firstItem(), 0, ',', '.') }}–{{ number_format($items->lastItem(), 0, ',', '.') }} dari {{ number_format($items->total(), 0, ',', '.') }} barang
            </p>
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</x-app-layout>
