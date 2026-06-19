<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'section_id'     => ['required', 'exists:sections,id'],
            'type'           => ['required', Rule::in(['multiple_choice','true_or_false','likert_scale','short_answer'])],
            'question_text'  => ['required', 'string'],
            'options'        => ['nullable', 'array'],
            'options.*'      => ['nullable', 'string', 'max:500'],
            'correct_answer' => [
                Rule::requiredIf(fn () => in_array($this->input('type'), ['multiple_choice', 'true_or_false'])),
                'nullable', 'string',
            ],
            'points'         => ['required', 'numeric', 'min:0'],
            'order'          => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('type') !== 'multiple_choice') {
                return;
            }

            $options = array_values(array_filter($this->input('options', [])));

            if (count($options) < 2) {
                $validator->errors()->add('options', 'Multiple choice questions need at least 2 answer choices.');
                return;
            }

            $correct = $this->input('correct_answer');
            if ($correct && ! in_array($correct, $options, true)) {
                $validator->errors()->add('correct_answer', 'The correct answer must exactly match one of the answer choices.');
            }
        });
    }
}
