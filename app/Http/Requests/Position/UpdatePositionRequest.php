<?php

namespace App\Http\Requests\Position;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('position')->id;

        return [
            'title'       => ['sometimes', 'string', 'max:255', Rule::unique('positions', 'title')->ignore($id)],
            'description' => ['nullable', 'string'],
        ];
    }
}