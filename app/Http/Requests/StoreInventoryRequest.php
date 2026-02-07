<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sku' => ['nullable', 'string', 'max:50', 'unique:inventories,sku'],
            'item_type' => ['required', 'in:gown,suit'],
            'name' => ['required', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:100'],
            'design' => ['required', 'string', 'max:255'],
            'rental_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'is_sellable' => ['nullable', 'boolean'],
            'selling_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'status_id' => ['nullable', 'exists:inventory_statuses,status_id'],
        ];
    }
}
