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
     */
    public function rules(): array
    {
        return [
            'reservation_id' => 'required|exists:reservations,reservation_id',
            'item_id' => 'required|exists:inventories,item_id',
            'customer_id' => 'required|exists:customers,customer_id',
            'released_date' => 'required|date',
            'due_date' => 'required|date|after:released_date',
        ];
    }
}
