<x-app-layout>
    <x-slot name="breadcrumb">
        <a href="{{ route('categories.index') }}">Kategori</a>
        <span class="separator">/</span>
        <span class="current">Edit Kategori</span>
    </x-slot>

    <div style="margin-bottom:1.5rem;">
        <h1 class="page-title"><i class="fas fa-pen" style="color:#4f46e5;margin-right:8px;"></i>Edit Kategori</h1>
        <p class="page-subtitle">Ubah nama kategori: <strong>{{ $category->name }}</strong></p>
    </div>

    <div style="max-width:500px;">
        <div class="card">
            <div class="card-header"><h2>Form Edit Kategori</h2></div>
            <div class="card-body">
                <form method="POST" action="{{ route('categories.update', $category) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Kategori <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name', $category->name) }}"
                            placeholder="Contoh: Alat Laboratorium, Elektronik..."
                            autofocus
                            required
                        >
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
                        <button type="submit" class="btn btn-primary" id="btn-update-kategori">
                            <i class="fas fa-floppy-disk"></i> Simpan Perubahan
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
