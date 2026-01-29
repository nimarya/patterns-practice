<?php

namespace App\Http\Requests\Courses;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'user_id' => ['required', 'exists:App\\Models\\User,id'],
            'type' => ['required', 'in:major_semester,major_year,minor_semester,minor_year'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }
}
