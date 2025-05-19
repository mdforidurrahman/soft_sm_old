<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use App\Rules\NoSpecialCharacters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new NoSpecialCharacters],
            'description' => 'required|string',
            'staff_id' => 'required|exists:users,id',
            'file.*' => 'file|mimes:jpeg,png,pdf,doc,docx|max:2048',
            'status' => ['required', new Enum(ProjectStatus::class)],
        ];
    }
}
