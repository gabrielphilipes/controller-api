<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMultipleUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            '*' => 'required|array',
            '*.name' => 'required|string',
            '*.email' => 'required|email',
            '*.password' => 'string|min:8',
            '*.permissions' => 'nullable|array',
        ];
    }
}
