<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('employee')->id;

        return [
            'name'        => ['sometimes', 'string', 'max:255'],
            'email'       => ['sometimes', 'email', Rule::unique('employees')->ignore($id)],
            'salary'      => ['sometimes', 'numeric', 'min:0'],
            ' '  => ['sometimes', 'nullable', 'exists:employees,id'],
            'position_id' => ['sometimes', 'nullable', 'exists:positions,id'], // ← add this
        ];
    }
}