<?php

namespace App\Http\Requests;

/**
 * Library Member Store Request
 * 
 * Prompt 370: Create Library Member Store Form Request
 * 
 * Validates library membership data.
 */
class LibraryMemberStoreRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-library-members');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Member Type
            'member_type' => ['required', 'in:student,teacher,staff'],
            
            // User ID
            'user_id' => ['required', 'exists:users,id'],
            
            // Card Number (unique)
            'card_number' => ['required', 'string', 'max:50', 'unique:library_members,card_number'],
            
            // Membership Start Date
            'start_date' => ['required', 'date'],
            
            // Membership End Date (optional, must be after start date)
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            
            // Status
            'status' => ['required', 'in:active,inactive'],
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
            'member_type.required' => 'The member type is required.',
            'member_type.in' => 'The member type must be student, teacher, or staff.',
            'user_id.required' => 'The user is required.',
            'user_id.exists' => 'The selected user is invalid.',
            'card_number.required' => 'The card number is required.',
            'card_number.string' => 'The card number must be a string.',
            'card_number.max' => 'The card number must not exceed 50 characters.',
            'card_number.unique' => 'This card number has already been taken.',
            'start_date.required' => 'The start date is required.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be active or inactive.',
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
            'member_type' => 'member type',
            'user_id' => 'user',
            'card_number' => 'card number',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'status' => 'status',
        ];
    }
}
