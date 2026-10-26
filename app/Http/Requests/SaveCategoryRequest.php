<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge(['name' => is_string($this->name) ? trim($this->name) : $this->name]);
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category?->id)],
        ];
    }

    public function messages(): array
    {
        return ['name.required' => 'Nama kategori wajib diisi.', 'name.unique' => 'Nama kategori sudah ada.'];
    }
}
