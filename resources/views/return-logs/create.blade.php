<x-app-layout>
    <x-slot name="breadcrumb">
        <a href="{{ route('return-logs.index') }}">Pengembalian</a>
        <span class="separator">/</span>
        <span class="current">Catat Pengembalian</span>
    </x-slot>

    <div class="page-heading"><div><h1 class="page-title">Catat Pengembalian</h1><p class="page-subtitle">Pilih transaksi aktif dan catat kondisi barang saat diterima.</p></div></div>

    <div class="form-card card">
        <div class="card-header"><h2>Informasi Pengembalian</h2></div>
        <div class="card-body">
            @if($loans->isEmpty())
                <div class="empty-state"><i class="fas fa-circle-check"></i><p>Tidak ada peminjaman aktif yang menunggu pengembalian.</p><a href="{{ route('return-logs.index') }}" class="btn btn-secondary">Kembali ke Riwayat</a></div>
            @else
                <form method="POST" action="{{ route('return-logs.store') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="loan_id">Peminjaman <span class="required">*</span></label>
                        <select class="form-control @error('loan_id') is-invalid @enderror" id="loan_id" name="loan_id" required autofocus>
                            <option value="">Pilih peminjaman</option>
                            @foreach($loans as $loan)
                                <option value="{{ $loan->id }}" @selected(old('loan_id', $selectedLoanId) == $loan->id)>
                                    #{{ $loan->id }} · {{ $loan->user->name }} · {{ $loan->item->name }} ({{ number_format($loan->quantity, 0, ',', '.') }} unit, jatuh tempo {{ $loan->due_date->locale('id')->isoFormat('D MMM Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('loan_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="returned_at">Tanggal Pengembalian <span class="required">*</span></label>
                        <input class="form-control @error('returned_at') is-invalid @enderror" id="returned_at" name="returned_at" type="date" value="{{ old('returned_at', now()->toDateString()) }}" max="{{ now()->toDateString() }}" placeholder="Pilih tanggal pengembalian" required>
                        @error('returned_at')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="condition_on_return">Kondisi Barang <span class="required">*</span></label>
                        <select class="form-control @error('condition_on_return') is-invalid @enderror" id="condition_on_return" name="condition_on_return" required>
                            <option value="">Pilih kondisi</option>
                            @foreach(['baik' => 'Baik', 'rusak ringan' => 'Rusak ringan', 'rusak berat' => 'Rusak berat'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('condition_on_return') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="page-subtitle">Barang berkondisi baik akan ditambahkan kembali ke stok tersedia.</small>
                        @error('condition_on_return')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="notes">Catatan</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" placeholder="Contoh: kelengkapan, kerusakan, atau catatan pemeriksaan...">{{ old('notes') }}</textarea>
                        @error('notes')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i> Simpan Pengembalian</button>
                        <a href="{{ route('return-logs.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
