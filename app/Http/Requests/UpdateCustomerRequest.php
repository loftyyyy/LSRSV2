<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'first_name' => ['sometimes', 'required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['sometimes', 'required', 'email:rfc,dns', 'max:255', 'unique:customers,email,' . $this->route('customer')->customer_id . ',customer_id'],
            'contact_number' => ['sometimes', 'required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:20'],
            'address' => ['sometimes', 'required', 'string', 'max:1000'],
            'measurement' => ['sometimes', 'nullable', 'array'],
            'status_id' => ['sometimes', 'nullable', 'exists:customer_statuses,status_id'],
        ];
    }
}
