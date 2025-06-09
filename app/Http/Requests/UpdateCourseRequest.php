<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
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
            //
            'name' => 'required|string|max:255',
            'path_trailer' => 'required|string|max:255',
            'about' => 'required|string|max:255',
            'category_id' => 'required|string|max:255',
            'course_mode_id' => 'required|exists:course_modes,id',
            'course_level_id' => 'required|exists:course_levels,id',
            'thumbnail' => 'sometimes|string|max:255',
            'price' => 'required|numeric|min:0',
            'trainer_id' => 'nullable|exists:trainers,id',
            'course_keypoints.*' => 'nullable|string|max:255',
        ];
    }
}
