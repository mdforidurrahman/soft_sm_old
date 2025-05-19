<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdatePurchaseRequest extends BaseFormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:contacts,id',
            'reference_no' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_status' => 'required|in:pending,completed',
            'address' => 'nullable|string',
//            'store_id' => 'required|string|max:255',
            'pay_term' => 'nullable|string|max:255',
            'pay_term_type' => 'nullable|in:days,months',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Supplier field is required.',
            'supplier_id.exists' => 'Supplier field does not exist.',
            'reference_no.required' => 'Reference no field is required.',
            'reference_no.string' => 'Reference no must be string.',
            'purchase_date.required' => 'Purchase date field is required.',
            'purchase_date.date' => 'Purchase date must be a valid date.',
            'purchase_status.required' => 'Purchase status field is required.',
            'store.required' => 'Shop Location field is required.',
            'store.string' => 'Shop Location must be string.',
            'store.max' => 'Shop Location maximum 255 characters.',
            'document.mimes' => 'Document must be pdf, doc, docx, jpeg, jpg, png.',
        ];
    }
}
