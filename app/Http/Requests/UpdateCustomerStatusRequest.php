<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerStatusRequest extends FormRequest
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
        $statusId = $this->route('customerStatus')->status_id ?? null;

        return [
            'status_name' => ['sometimes', 'required', 'string', 'max:255', 'unique:customer_statuses,status_name,' . $statusId . ',status_id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
