<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReturnLogRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['loan_id' => ['nullable', 'integer', 'exists:loans,id']]; }
}
