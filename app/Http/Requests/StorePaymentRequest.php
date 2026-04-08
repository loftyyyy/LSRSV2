<?php

namespace App\Http\Requests;

use App\Services\PaymentService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
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
            'invoice_id' => [
                'required',
                'integer',
                'exists:invoices,invoice_id',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999.99',
            ],
            'payment_method' => [
                'required',
                'string',
                Rule::in(array_keys(PaymentService::PAYMENT_METHODS)),
            ],
            'status_id' => [
                'nullable',
                'integer',
                'exists:payment_statuses,status_id',
            ],
            'payment_date' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'payment_reference' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'invoice_id.required' => 'Please select an invoice to apply this payment to.',
            'invoice_id.exists' => 'The selected invoice does not exist.',
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Payment amount must be at least ₱0.01.',
            'amount.max' => 'Payment amount cannot exceed ₱9,999,999.99.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected. Allowed methods: cash, card, gcash, paymaya, bank_transfer.',
            'status_id.exists' => 'The selected payment status does not exist.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
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
            'invoice_id' => 'invoice',
            'payment_method' => 'payment method',
            'payment_date' => 'payment date',
        ];
    }
}
