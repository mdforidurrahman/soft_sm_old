<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreStoreRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */

    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array {
        return [
            'name.required' => 'The store name is required.',
            'name.max' => 'The store name must not exceed 255 characters.',
            'address.required' => 'The store address is required.',
            'address.max' => 'The store address must not exceed 500 characters.',
        ];
    }
}
