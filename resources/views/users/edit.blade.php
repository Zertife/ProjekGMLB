<x-app-layout>
    <x-slot name="breadcrumb">
        <a href="{{ route('users.index') }}">Manajemen User</a>
        <span class="separator">/</span>
        <span class="current">Edit User</span>
    </x-slot>

    <div style="margin-bottom:1.5rem;">
        <h1 class="page-title"><i class="fas fa-user-pen" style="color:#4f46e5;margin-right:8px;"></i>Edit User</h1>
        <p class="page-subtitle">Perbarui informasi akun pengguna: <strong>{{ $user->name }}</strong></p>
    </div>

    <div style="max-width:600px;">
        <div class="card">
            <div class="card-header">
                <h2>Form Edit User</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name', $user->name) }}"
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
                            value="{{ old('email', $user->email) }}"
                            placeholder="contoh@email.com"
                            required
                        >
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Role <span class="required">*</span></label>
                        <select id="role" name="role" class="form-control {{ $errors->has('role') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                        </select>
                        @error('role') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
                        <button type="submit" class="btn btn-primary" id="btn-update-user">
                            <i class="fas fa-floppy-disk"></i> Simpan Perubahan
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
