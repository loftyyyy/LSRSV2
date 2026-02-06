<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Or implement your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'sometimes|exists:customers,customer_id',
            'status_id' => 'sometimes|exists:reservation_statuses,status_id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',

            // Items array validation
            'items' => 'sometimes|array',
            'items.*.reservation_item_id' => 'sometimes|exists:reservation_items,reservation_item_id',
            'items.*.item_id' => 'required_with:items|exists:inventories,item_id',
            'items.*.quantity' => 'sometimes|integer|min:1',
            'items.*.rental_price' => 'sometimes|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',

            // Flag to replace all items
            'replace_items' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_id.exists' => 'The selected customer does not exist',
            'status_id.exists' => 'Invalid reservation status',
            'start_date.date' => 'Start date must be a valid date',
            'end_date.date' => 'End date must be a valid date',
            'end_date.after' => 'End date must be after start date',
            'items.array' => 'Items must be an array',
            'items.*.reservation_item_id.exists' => 'Invalid reservation item',
            'items.*.item_id.required_with' => 'Item ID is required',
            'items.*.item_id.exists' => 'The selected item does not exist',
            'items.*.quantity.integer' => 'Quantity must be a number',
            'items.*.quantity.min' => 'Quantity must be at least 1',
            'items.*.rental_price.numeric' => 'Rental price must be a number',
            'items.*.rental_price.min' => 'Rental price cannot be negative',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'customer',
            'status_id' => 'status',
            'start_date' => 'rental start date',
            'end_date' => 'rental end date',
            'items.*.item_id' => 'item',
            'items.*.quantity' => 'quantity',
            'items.*.rental_price' => 'rental price',
        ];
    }
}
