<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityLogIndexRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return ['search' => ['nullable', 'string', 'max:100'], 'user_id' => ['nullable', 'integer', 'exists:users,id'], 'action' => ['nullable', 'string', 'max:255']];
    }
}
