<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateLoanRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['item_id' => ['nullable', 'integer', Rule::exists('items', 'id')->whereNull('deleted_at')]]; }
}
