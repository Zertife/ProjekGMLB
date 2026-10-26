<x-app-layout>
    <x-slot name="breadcrumb">
        <a href="{{ route('items.index') }}">Daftar Barang</a>
        <span class="separator">/</span>
        <span class="current">Edit Barang</span>
    </x-slot>

    <div style="margin-bottom:1.5rem;">
        <h1 class="page-title"><i class="fas fa-pen-to-square" style="color:#4f46e5;margin-right:8px;"></i>Edit Barang</h1>
        <p class="page-subtitle">Perbarui informasi barang: <strong>{{ $item->name }}</strong></p>
    </div>

    <div style="max-width:650px;">
        <div class="card">
            <div class="card-header">
                <h2>Form Edit Barang</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('items.update', $item) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="category_id" class="form-label">Kategori Barang <span class="required">*</span></label>
                        <select id="category_id" name="category_id" class="form-control {{ $errors->has('category_id') ? 'is-invalid' : '' }}" required autofocus>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">Nama Barang <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name', $item->name) }}"
                            placeholder="Contoh: Proyektor Epson EB-X500..."
                            required
                        >
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label for="code" class="form-label">Kode / SKU Barang <span class="required">*</span></label>
                            <input
                                type="text"
                                id="code"
                                name="code"
                                class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}"
                                value="{{ old('code', $item->code) }}"
                                placeholder="Contoh: PRJ-001..."
                                required
                            >
                            @error('code') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label for="stock" class="form-label">Jumlah Stok <span class="required">*</span></label>
                            <input
                                type="number"
                                id="stock"
                                name="stock"
                                min="0"
                                class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}"
                                value="{{ old('stock', $item->stock) }}"
                                placeholder="Masukkan jumlah stok..."
                                required
                            >
                            @error('stock') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="condition" class="form-label">Kondisi Barang <span class="required">*</span></label>
                        <select id="condition" name="condition" class="form-control {{ $errors->has('condition') ? 'is-invalid' : '' }}" required>
                            <option value="baik" {{ old('condition', $item->condition) === 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak ringan" {{ old('condition', $item->condition) === 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak berat" {{ old('condition', $item->condition) === 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                        @error('condition') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
                        <button type="submit" class="btn btn-primary" id="btn-update-barang">
                            <i class="fas fa-floppy-disk"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('items.index') }}" class="btn btn-secondary">
                            <i class="fas fa-xmark"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
