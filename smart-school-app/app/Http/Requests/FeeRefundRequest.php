<?php

namespace App\Http\Requests;

/**
 * Fee Refund Request
 * 
 * Prompt 368: Create Fee Refund Form Request
 * 
 * Validates refund request data.
 */
class FeeRefundRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-fee-refunds');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Transaction ID (required, must exist)
            'transaction_id' => ['required', 'exists:fees_transactions,id'],
            
            // Refund Amount
            'refund_amount' => ['required', 'numeric', 'min:0'],
            
            // Refund Method
            'refund_method' => ['required', 'in:cash,cheque,dd,online'],
            
            // Reason for Refund
            'reason' => ['required', 'string', 'max:500'],
            
            // Reference Number (optional)
            'reference_number' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    protected function customMessages(): array
    {
        return [
            'transaction_id.required' => 'The transaction is required.',
            'transaction_id.exists' => 'The selected transaction is invalid.',
            'refund_amount.required' => 'The refund amount is required.',
            'refund_amount.numeric' => 'The refund amount must be a number.',
            'refund_amount.min' => 'The refund amount must be at least 0.',
            'refund_method.required' => 'The refund method is required.',
            'refund_method.in' => 'The refund method must be cash, cheque, dd, or online.',
            'reason.required' => 'The reason for refund is required.',
            'reason.string' => 'The reason must be a string.',
            'reason.max' => 'The reason must not exceed 500 characters.',
            'reference_number.string' => 'The reference number must be a string.',
            'reference_number.max' => 'The reference number must not exceed 100 characters.',
        ];
    }

    /**
     * Get custom attribute names.
     *
     * @return array
     */
    protected function customAttributes(): array
    {
        return [
            'transaction_id' => 'transaction',
            'refund_amount' => 'refund amount',
            'refund_method' => 'refund method',
            'reason' => 'reason',
            'reference_number' => 'reference number',
        ];
    }
}
