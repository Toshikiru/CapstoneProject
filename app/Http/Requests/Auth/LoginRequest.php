<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'string', 'max:50'],
            'password'   => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Student ID is required.',
            'password.required'   => 'Password is required.',
        ];
    }
}
