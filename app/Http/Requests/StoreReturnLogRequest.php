<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReturnLogRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'loan_id' => ['required', 'integer', Rule::exists('loans', 'id')->whereNull('deleted_at')],
            'returned_at' => ['required', 'date', 'before_or_equal:today'],
            'condition_on_return' => ['required', Rule::in(['baik', 'rusak ringan', 'rusak berat'])],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'loan_id.required' => 'Peminjaman wajib dipilih.', 'loan_id.exists' => 'Data peminjaman tidak ditemukan.',
            'returned_at.required' => 'Tanggal pengembalian wajib diisi.', 'returned_at.before_or_equal' => 'Tanggal pengembalian tidak boleh melebihi hari ini.',
            'condition_on_return.required' => 'Kondisi barang saat dikembalikan wajib dipilih.', 'condition_on_return.in' => 'Kondisi barang saat dikembalikan tidak valid.',
            'notes.max' => 'Catatan maksimal 5.000 karakter.',
        ];
    }
}
