<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id' => [Rule::requiredIf(fn () => $this->user()?->isAdmin()), 'nullable', 'integer', Rule::exists('users', 'id')->where('role', 'user')],
            'item_id' => ['required', 'integer', Rule::exists('items', 'id')->whereNull('deleted_at')],
            'quantity' => ['required', 'integer', 'min:1'],
            'loan_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:loan_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Peminjam wajib dipilih.', 'user_id.exists' => 'Data peminjam tidak valid.',
            'item_id.required' => 'Barang wajib dipilih.', 'item_id.exists' => 'Barang tidak ditemukan atau sudah dihapus.',
            'quantity.required' => 'Jumlah barang wajib diisi.', 'quantity.integer' => 'Jumlah barang harus berupa angka.',
            'quantity.min' => 'Jumlah peminjaman minimal 1.', 'loan_date.required' => 'Tanggal peminjaman wajib diisi.',
            'loan_date.date' => 'Format tanggal peminjaman tidak valid.', 'due_date.required' => 'Tanggal pengembalian wajib diisi.',
            'due_date.date' => 'Format tanggal pengembalian tidak valid.', 'due_date.after_or_equal' => 'Tanggal pengembalian harus sama atau setelah tanggal pinjam.',
        ];
    }
}
