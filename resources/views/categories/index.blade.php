<x-app-layout>
    <x-slot name="breadcrumb">
        <span class="current">Kategori</span>
    </x-slot>

    <div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 class="page-title"><i class="fas fa-tags" style="color:#4f46e5;margin-right:8px;"></i>Manajemen Kategori</h1>
            <p class="page-subtitle">Kelola kategori barang inventaris sekolah.</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-primary" id="btn-tambah-kategori">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    </div>

    <div class="card">
        <div class="card-header" style="flex-wrap:wrap; gap:0.75rem;">
            <h2>Daftar Kategori <span class="badge badge-secondary" style="margin-left:6px;">{{ number_format($categories->total(), 0, ',', '.') }}</span></h2>
            <form method="GET" action="{{ route('categories.index') }}" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    style="width:220px;"
                    placeholder="Cari nama kategori..."
                    value="{{ request('search') }}"
                    autofocus
                >
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-xmark"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="table-wrapper">
            @if($categories->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-tags"></i>
                    <p>Belum ada kategori{{ request('search') ? ' untuk pencarian "' . request('search') . '"' : '' }}.</p>
                </div>
            @else
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Nama Kategori</th>
                            <th>Jumlah Barang</th>
                            <th>Dibuat</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td style="color:#94a3b8;">{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.65rem;">
                                    <div style="width:32px;height:32px;border-radius:8px;background:#eef2ff;display:flex;align-items:center;justify-content:center;font-size:0.8rem;color:#4f46e5;">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <span style="font-weight:500;color:#1e293b;">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $category->items_count > 0 ? 'badge-info' : 'badge-secondary' }}">
                                    {{ number_format($category->items_count, 0, ',', '.') }} barang
                                </span>
                            </td>
                            <td style="font-size:0.8rem;color:#64748b;">{{ $category->created_at->locale('id')->isoFormat('D MMM Y') }}</td>
                            <td>
                                <div style="display:flex;justify-content:center;gap:0.4rem;">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm" title="Edit Kategori">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @if($category->items_count === 0)
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori {{ addslashes($category->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Kategori">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-danger btn-sm" disabled title="Tidak dapat dihapus (ada {{ number_format($category->items_count, 0, ',', '.') }} barang)">
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

        @if($categories->hasPages())
        <div style="padding:1rem 1.25rem; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
            <p style="font-size:0.8rem;color:#64748b;">
                Menampilkan {{ number_format($categories->firstItem(), 0, ',', '.') }}–{{ number_format($categories->lastItem(), 0, ',', '.') }} dari {{ number_format($categories->total(), 0, ',', '.') }} kategori
            </p>
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</x-app-layout>
