<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanIndexRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'user')],
            'status' => ['nullable', Rule::in(['dipinjam', 'dikembalikan', 'terlambat'])],
        ];
    }
}
