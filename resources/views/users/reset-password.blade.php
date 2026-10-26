<x-app-layout>
    <x-slot name="breadcrumb">
        <a href="{{ route('users.index') }}">Manajemen User</a>
        <span class="separator">/</span>
        <span class="current">Reset Password</span>
    </x-slot>

    <div style="margin-bottom:1.5rem;">
        <h1 class="page-title"><i class="fas fa-key" style="color:#f59e0b;margin-right:8px;"></i>Reset Password</h1>
        <p class="page-subtitle">Reset password akun: <strong>{{ $user->name }}</strong> ({{ $user->email }})</p>
    </div>

    <div style="max-width:500px;">
        <div class="card">
            <div class="card-header">
                <h2>Form Reset Password</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>Password lama user akan diganti. User harus login ulang menggunakan password baru.</span>
                </div>

                <form method="POST" action="{{ route('users.reset-password', $user) }}">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru <span class="required">*</span></label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Minimal 8 karakter..."
                            autofocus
                            required
                        >
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="required">*</span></label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi password baru..."
                            required
                        >
                    </div>

                    <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
                        <button type="submit" class="btn btn-warning" id="btn-reset-password">
                            <i class="fas fa-key"></i> Reset Password
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
