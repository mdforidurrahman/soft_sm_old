<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends BaseFormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'business_store_id' => 'required|exists:stores,id',
            'reference_no' => 'required|unique:purchases',
            'purchase_date' => 'required|date',
            'purchase_status' => 'required|string',
            'payment_term' => 'nullable|string',
            'items' => 'required',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'document' => 'nullable|max:5120',

            'advance_balance' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_account' => 'nullable|string',
            'payment_note' => 'nullable|string',
            'payment_due_date' => 'nullable',
            'is_advance_payment' => 'boolean',

            // Discount validation
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|between:0,100',

            // Shipping validation
            'shipping_address' => 'nullable|string',
            'shipping_method' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'expected_delivery_date' => 'nullable|date',
            'tracking_number' => 'nullable|string',
        ];
    }

    public function messages(): array {
        return [

            'reference_no.required' => 'Reference no field is required.',
            'reference_no.string' => 'Reference no must be string.',
            'purchase_date.required' => 'Purchase date field is required.',
            'purchase_date.date' => 'Purchase date must be a valid date.',
            'purchase_status.required' => 'Purchase status field is required.',
            'business_location.required' => 'Shop Location field is required.',
            'business_location.string' => 'Shop Location must be string.',
            'business_location.max' => 'Shop Location maximum 255 characters.',
            'document.mimes' => 'Document must be pdf, doc, docx, jpeg, jpg, png.',
        ];
    }
}
