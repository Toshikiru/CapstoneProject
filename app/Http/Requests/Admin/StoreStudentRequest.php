<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('student')?->id;

        return [
            'student_id'               => ['required', 'string', 'max:50', Rule::unique('users', 'student_id')->ignore($userId)],
            'name'                     => ['required', 'string', 'max:255'],
            'password'                 => [$this->isMethod('POST') ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
            'first_name'               => ['required', 'string', 'max:100'],
            'middle_name'              => ['nullable', 'string', 'max:100'],
            'last_name'                => ['required', 'string', 'max:100'],
            'suffix'                   => ['nullable', 'string', 'max:20'],
            'sex'                      => ['required', Rule::in(['Male', 'Female'])],
            'date_of_birth'            => ['required', 'date', 'before:today'],
            'address'                  => ['required', 'string', 'max:500'],
            'contact_number'           => ['nullable', 'string', 'max:20'],
            'guardian_name'            => ['nullable', 'string', 'max:255'],
            'guardian_contact_number'  => ['nullable', 'string', 'max:20'],
            'course'                   => ['required', 'string', 'max:100'],
            'year_level'               => ['required', 'string', 'max:20'],
        ];
    }
}
