<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin','trainer']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'about' => 'required|string',
            'slug' => 'nullable|string|unique:courses,slug',
            'thumbnail' => 'nullable|string',
            'price' => 'required|numeric',
            'trainer' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'course_mode_id' => 'required|exists:course_modes,id',
            'course_level_id' => 'required|exists:course_levels,id',
        ];
    }
    
}
