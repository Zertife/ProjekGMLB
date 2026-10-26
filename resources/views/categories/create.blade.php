<x-app-layout>
    <x-slot name="breadcrumb">
        <a href="{{ route('categories.index') }}">Kategori</a>
        <span class="separator">/</span>
        <span class="current">Tambah Kategori</span>
    </x-slot>

    <div style="margin-bottom:1.5rem;">
        <h1 class="page-title"><i class="fas fa-tag" style="color:#4f46e5;margin-right:8px;"></i>Tambah Kategori</h1>
        <p class="page-subtitle">Tambahkan kategori barang baru ke sistem.</p>
    </div>

    <div style="max-width:500px;">
        <div class="card">
            <div class="card-header"><h2>Form Tambah Kategori</h2></div>
            <div class="card-body">
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Kategori <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Alat Laboratorium, Alat Olahraga..."
                            autofocus
                            required
                        >
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
                        <button type="submit" class="btn btn-primary" id="btn-simpan-kategori">
                            <i class="fas fa-floppy-disk"></i> Simpan Kategori
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-xmark"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
