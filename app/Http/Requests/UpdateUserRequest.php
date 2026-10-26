<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ];
    }

    public function messages(): array
    {
        return ['name.required' => 'Nama wajib diisi.', 'email.required' => 'Email wajib diisi.', 'email.unique' => 'Email sudah digunakan.', 'role.required' => 'Role wajib dipilih.', 'role.in' => 'Role tidak valid.'];
    }
}
