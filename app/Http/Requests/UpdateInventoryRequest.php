<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
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
        $inventoryId = $this->route('inventory')->item_id ?? null;

        return [
            'sku' => ['nullable', 'string', 'max:50', 'unique:inventories,sku,' . $inventoryId . ',item_id'],
            'item_type' => ['sometimes', 'required', 'in:gown,suit'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'size' => ['sometimes', 'required', 'string', 'max:50'],
            'color' => ['sometimes', 'required', 'string', 'max:100'],
            'design' => ['sometimes', 'required', 'string', 'max:255'],
            'condition' => ['sometimes', 'required', 'in:good,damaged,under repaired,retired'],
            'rental_price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:999999.99'],
            'is_sellable' => ['sometimes', 'boolean'],
            'selling_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'status_id' => ['sometimes', 'required', 'exists:inventory_statuses,status_id'],
        ];
    }
}
