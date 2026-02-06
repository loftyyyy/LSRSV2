<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
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
            'customer_id' => 'required|exists:customers,customer_id',
            'status_id' => 'sometimes|exists:reservation_statuses,status_id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',

            // Items array validation
            'items' => 'sometimes|array|min:1',
            'items.*.item_id' => 'required_with:items|exists:inventories,item_id',
            'items.*.quantity' => 'sometimes|integer|min:1',
            'items.*.rental_price' => 'sometimes|numeric|min:0',
            'items.*.notes' => 'sometimes|string|max:500',
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
            'customer_id.required' => 'Please select a customer',
            'customer_id.exists' => 'The selected customer does not exist',
            'status_id.required' => 'Reservation status is required',
            'status_id.exists' => 'Invalid reservation status',
            'start_date.required' => 'Start date is required',
            'start_date.after_or_equal' => 'Start date must be today or later',
            'end_date.required' => 'End date is required',
            'end_date.after' => 'End date must be after start date',
            'items.array' => 'Items must be an array',
            'items.min' => 'At least one item must be selected',
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
