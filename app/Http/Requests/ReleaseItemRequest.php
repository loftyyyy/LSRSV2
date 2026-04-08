<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReleaseItemRequest extends FormRequest
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
            // Item selection - optional if releasing from reservation (reservation_item_id will be used)
            'item_id' => [
                'required_without:reservation_item_id',
                'nullable',
                'integer',
                'exists:inventories,item_id',
            ],

            // Reservation item ID - optional, used when releasing from a confirmed reservation
            'reservation_item_id' => [
                'required_without:item_id',
                'nullable',
                'integer',
                'exists:reservation_items,reservation_item_id',
            ],

            // Customer information
            'customer_id' => [
                'required',
                'integer',
                'exists:customers,customer_id',
            ],

            // Dates
            'released_date' => [
                'required',
                'date',
                'before_or_equal:now',
            ],
            'due_date' => [
                'required',
                'date',
                'after:released_date',
            ],

            // Reservation (optional, for tracking purposes)
            'reservation_id' => [
                'nullable',
                'integer',
                'exists:reservations,reservation_id',
            ],

            // Rental payment collection (replacing old deposit collection)
            'collect_rental_payment' => [
                'sometimes',
                'boolean',
            ],
            'rental_payment_method' => [
                'nullable',
                'string',
                'in:cash,card,gcash,paymaya,bank_transfer',
            ],

            // Notes
            'release_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'item_id.required_without' => 'Physical item ID or reservation item ID is required to release an item.',
            'item_id.exists' => 'The selected item does not exist in inventory.',
            'reservation_item_id.required_without' => 'Reservation item ID or physical item ID is required to release an item.',
            'reservation_item_id.exists' => 'The selected reservation item does not exist.',
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'released_date.required' => 'Release date is required.',
            'released_date.before_or_equal' => 'Release date cannot be in the future.',
            'due_date.required' => 'Due date is required.',
            'due_date.after' => 'Due date must be after the release date.',
            'rental_payment_method.in' => 'Invalid payment method. Must be one of: cash, card, gcash, paymaya, bank_transfer.',
        ];
    }

    /**
     * Custom attributes
     */
    public function attributes(): array
    {
        return [
            'item_id' => 'physical item',
            'reservation_item_id' => 'reservation item',
            'customer_id' => 'customer',
            'released_date' => 'release date',
            'due_date' => 'due date',
            'collect_rental_payment' => 'rental payment collection',
            'rental_payment_method' => 'payment method',
        ];
    }
}
