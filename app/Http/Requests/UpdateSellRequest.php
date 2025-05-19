<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ensure this is set based on your authorization logic
    }

    public function rules()
    {
        return [
            'store_id' => 'required|integer|exists:stores,id',
            'customer_id' => 'required|integer|exists:contacts,id',
            
            
            'sell_date' => 'required|date',
            'sell_status' => 'required|string|in:completed,pending,cancelled',
            'pay_term' => 'nullable|integer|min:0',
            'pay_term_type' => 'nullable|string|in:days,months',
           

            // Financial fields
            'total_before_tax' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:fixed,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'net_total' => 'nullable|numeric|min:0',
            'advance_balance' => 'nullable|numeric|min:0',
            'payment_due' => 'nullable|numeric|min:0',
           
            'payment_due_date' => 'nullable|date',

            // Additional notes
            'additional_notes' => 'nullable|string|max:500',

            // Items
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|integer|exists:sell_items,id', // Optional if item already exists
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.profit_margin' => 'required|numeric|min:0|max:100',

            // Shipping details
            'shipping_address' => 'nullable|string|max:255',
            'shipping_method' => 'nullable|string|max:50',
            'shipping_cost' => 'nullable|numeric|min:0',
            'expected_delivery_date' => 'nullable|date',
            'tracking_number' => 'nullable|string|max:50',
            'shipping_status' => 'nullable|string|in:pending,in_transit,delivered,cancelled',

            // Payment details
            'advance_balance' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|in:cash,credit_card,bank_transfer',
            'payment_account' => 'nullable|string|max:50',
            'payment_note' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'store_id.required' => 'The store ID is required.',
            'customer_id.required' => 'The customer ID is required.',
            'sell_date.required' => 'The sell date is required.',
            'sell_status.required' => 'The sell status is required.',
            'payment_status.required' => 'The payment status is required.',
            'items.required' => 'At least one item is required for the sell record.',
            'items.*.product_id.required' => 'The product ID is required for each item.',
            'items.*.quantity.required' => 'The quantity is required for each item.',
            'items.*.unit_cost.required' => 'The unit cost is required for each item.',
            'items.*.profit_margin.required' => 'The profit margin is required for each item.',
        ];
    }
}
