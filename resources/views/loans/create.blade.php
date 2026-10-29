<x-app-layout>
    <x-slot name="breadcrumb">
        <a href="{{ route('loans.index') }}">Data Peminjaman</a>
        <span class="separator">/</span>
        <span class="current">{{ auth()->user()->isAdmin() ? 'Catat Peminjaman' : 'Ajukan Peminjaman' }}</span>
    </x-slot>

    <div style="margin-bottom:1.5rem;">
        <h1 class="page-title"><i class="fas fa-handshake" style="color:#2563eb;margin-right:8px;"></i>{{ auth()->user()->isAdmin() ? 'Catat Peminjaman Barang' : 'Form Pengajuan Peminjaman' }}</h1>
        <p class="page-subtitle">{{ auth()->user()->isAdmin() ? 'Catat transaksi peminjaman langsung untuk staf/guru/siswa.' : 'Isi formulir untuk meminjam fasilitas atau peralatan sekolah.' }}</p>
    </div>

    <div style="max-width:650px;">
        <div class="card">
            <div class="card-header">
                <h2>Informasi Peminjaman</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('loans.store') }}">
                    @csrf

                    @if(auth()->user()->isAdmin())
                        <div class="form-group">
                            <label for="user_id" class="form-label">Peminjam (User/Siswa/Guru) <span class="required">*</span></label>
                            <select id="user_id" name="user_id" class="form-control {{ $errors->has('user_id') ? 'is-invalid' : '' }}" required autofocus>
                                <option value="">-- Pilih Peminjam --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="item_id" class="form-label">Pilih Barang Inventaris <span class="required">*</span></label>
                        <select id="item_id" name="item_id" class="form-control {{ $errors->has('item_id') ? 'is-invalid' : '' }}" required {{ !auth()->user()->isAdmin() ? 'autofocus' : '' }} onchange="updateMaxStock(this)">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" data-stock="{{ $item->stock }}" {{ old('item_id', $selectedItemId) == $item->id ? 'selected' : '' }}>
                                    [{{ $item->code }}] {{ $item->name }} — Tersedia: {{ number_format($item->stock, 0, ',', '.') }} unit (Kondisi: {{ $item->condition }})
                                </option>
                            @endforeach
                        </select>
                        @error('item_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="quantity" class="form-label">Jumlah Pinjam <span class="required">*</span></label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            min="1"
                            class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}"
                            value="{{ old('quantity', 1) }}"
                            placeholder="Jumlah barang yang dipinjam..."
                            required
                        >
                        <small id="stock-help" style="color:#64748b;font-size:0.75rem;margin-top:2px;display:block;">
                            Pastikan jumlah tidak melebihi stok yang tersedia.
                        </small>
                        @error('quantity') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="loan_date" class="form-label">Tanggal Pinjam <span class="required">*</span></label>
                            <input
                                type="date"
                                id="loan_date"
                                name="loan_date"
                                class="form-control {{ $errors->has('loan_date') ? 'is-invalid' : '' }}"
                                placeholder="Pilih tanggal peminjaman"
                                value="{{ old('loan_date', date('Y-m-d')) }}"
                                required
                            >
                            @error('loan_date') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label for="due_date" class="form-label">Tanggal Jatuh Tempo <span class="required">*</span></label>
                            <input
                                type="date"
                                id="due_date"
                                name="due_date"
                                class="form-control {{ $errors->has('due_date') ? 'is-invalid' : '' }}"
                                placeholder="Pilih tanggal jatuh tempo"
                                value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}"
                                required
                            >
                            @error('due_date') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
                        <button type="submit" class="btn btn-primary" id="btn-submit-peminjaman">
                            <i class="fas fa-paper-plane"></i> {{ auth()->user()->isAdmin() ? 'Simpan Peminjaman' : 'Kirim Pengajuan' }}
                        </button>
                        <a href="{{ route('loans.index') }}" class="btn btn-secondary">
                            <i class="fas fa-xmark"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateMaxStock(select) {
            const selectedOption = select.options[select.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock');
            const qtyInput = document.getElementById('quantity');
            const helpText = document.getElementById('stock-help');

            if (stock) {
                qtyInput.max = stock;
                helpText.innerText = `Maksimal unit yang dapat dipinjam: ${stock} unit.`;
            } else {
                qtyInput.removeAttribute('max');
                helpText.innerText = 'Pastikan jumlah tidak melebihi stok yang tersedia.';
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('item_id');
            if (select.value) {
                updateMaxStock(select);
            }
        });
    </script>
</x-app-layout>
