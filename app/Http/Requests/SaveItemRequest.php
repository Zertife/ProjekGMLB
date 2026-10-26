<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
            'code' => is_string($this->code) ? strtoupper(trim($this->code)) : $this->code,
        ]);
    }

    public function rules(): array
    {
        $item = $this->route('item');

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('items', 'code')->ignore($item?->id)],
            'stock' => ['required', 'integer', 'min:0'],
            'condition' => ['required', Rule::in(['baik', 'rusak ringan', 'rusak berat'])],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.', 'category_id.exists' => 'Kategori tidak valid.',
            'name.required' => 'Nama barang wajib diisi.', 'code.required' => 'Kode/SKU barang wajib diisi.',
            'code.unique' => 'Kode barang sudah digunakan.', 'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa bilangan bulat.', 'stock.min' => 'Stok tidak boleh bernilai negatif.',
            'condition.required' => 'Kondisi barang wajib dipilih.', 'condition.in' => 'Kondisi barang tidak valid.',
        ];
    }
}
