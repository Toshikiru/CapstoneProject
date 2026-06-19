<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'instructions'   => ['nullable', 'string'],
            'time_limit'     => ['required', 'integer', 'min:1', 'max:480'],
            'passing_score'  => ['required', 'numeric', 'min:0', 'max:100'],
            'max_attempts'   => ['required', 'integer', 'min:1'],
            'available_from' => ['nullable', 'date'],
            'available_until'=> ['nullable', 'date', 'after:available_from'],
        ];
    }
}
