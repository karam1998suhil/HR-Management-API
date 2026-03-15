<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest  // ✅ correct class name
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:employees,email'],
            'salary'     => ['required', 'numeric', 'min:0'],
            'is_founder' => ['sometimes', 'boolean'],
            'manager_id' => [
                Rule::requiredIf(fn() => ! $this->boolean('is_founder')),
                'nullable',
                'exists:employees,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'manager_id.required' => 'Every employee except the founder must have a manager.',
        ];
    }
}