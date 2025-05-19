<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store_id' => 'required|exists:stores,id',
            'customer_id' => 'required|exists:contacts,id',
            
            
            'sell_date' => 'required|date',
            'sell_status' => 'required|string',

            'pay_term' => 'nullable|string',
            'pay_term_type' => 'nullable|string',

            

            // Discount and Tax validation
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|between:0,100',
            'total_before_tax' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',

            // Sell items validation
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|between:0,100',
            'items.*.profit_margin' => 'nullable|numeric|between:0,100',

            'advance_balance' => 'nullable|numeric|min:0',
            'payment_due' => 'nullable|numeric|min:0',
            'payment_status' => 'required|string|in:completed,pending,overdue,partial',
            'payment_due_date' => 'nullable|date',

            // Shipping validation
            'shipping_address' => 'nullable|string',
            'shipping_method' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'expected_delivery_date' => 'nullable|date',
            'tracking_number' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'store_id.required' => 'Store field is required.',
            'store_id.exists' => 'Store does not exist.',
            'customer_id.required' => 'Customer field is required.',
            'customer_id.exists' => 'Customer does not exist.',
            
            'invoice_no.required' => 'Invoice number is required.',
            'invoice_no.unique' => 'Invoice number must be unique.',
            'sell_date.required' => 'Sell date is required.',
            'sell_date.date' => 'Sell date must be a valid date.',
            'sell_status.required' => 'Sell status is required.',

           

            // Discount validation messages
            'discount_type.in' => 'Discount type must be either "percentage" or "fixed".',
            'discount_percentage.between' => 'Discount percentage must be between 0 and 100.',

            // Item-specific validation messages
            'items.required' => 'Items field is required.',
            'items.*.product_id.required' => 'Product ID for each item is required.',
            'items.*.quantity.required' => 'Quantity for each item is required.',
            'items.*.unit_cost.required' => 'Unit cost for each item is required.',
            'items.*.discount_percent.between' => 'Discount percent must be between 0 and 100.',

            // Shipping validation messages
            'shipping_address.string' => 'Shipping address must be a valid string.',
            'shipping_cost.min' => 'Shipping cost must be a positive number.',
            'expected_delivery_date.date' => 'Expected delivery date must be a valid date.',
        ];
    }
}
