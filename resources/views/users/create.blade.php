<x-app-layout>
    <x-slot name="breadcrumb">
        <a href="{{ route('users.index') }}">Manajemen User</a>
        <span class="separator">/</span>
        <span class="current">Tambah User</span>
    </x-slot>

    <div style="margin-bottom:1.5rem;">
        <h1 class="page-title"><i class="fas fa-user-plus" style="color:#4f46e5;margin-right:8px;"></i>Tambah User Baru</h1>
        <p class="page-subtitle">Buat akun pengguna baru untuk sistem inventaris.</p>
    </div>

    <div style="max-width:600px;">
        <div class="card">
            <div class="card-header">
                <h2>Form Tambah User</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name') }}"
                            placeholder="Masukkan nama lengkap..."
                            autofocus
                            required
                        >
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email <span class="required">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="contoh@email.com"
                            required
                        >
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Role <span class="required">*</span></label>
                        <select id="role" name="role" class="form-control {{ $errors->has('role') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        </select>
                        @error('role') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password <span class="required">*</span></label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Minimal 8 karakter..."
                            required
                        >
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="required">*</span></label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi password..."
                            required
                        >
                    </div>

                    <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
                        <button type="submit" class="btn btn-primary" id="btn-simpan-user">
                            <i class="fas fa-floppy-disk"></i> Simpan User
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-xmark"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
