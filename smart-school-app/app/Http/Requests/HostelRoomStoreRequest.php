<?php

namespace App\Http\Requests;

/**
 * Hostel Room Store Request
 * 
 * Prompt 372: Create Hostel Room Store Form Request
 * 
 * Validates hostel room data.
 */
class HostelRoomStoreRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-hostel-rooms');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Hostel ID
            'hostel_id' => ['required', 'exists:hostels,id'],
            
            // Room Type ID
            'room_type_id' => ['required', 'exists:hostel_room_types,id'],
            
            // Room Number
            'room_number' => ['required', 'string', 'max:50'],
            
            // Capacity
            'capacity' => ['required', 'integer', 'min:1'],
            
            // Rent
            'rent' => ['required', 'numeric', 'min:0'],
            
            // Status
            'status' => ['required', 'in:available,occupied,maintenance'],
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
            'hostel_id.required' => 'The hostel is required.',
            'hostel_id.exists' => 'The selected hostel is invalid.',
            'room_type_id.required' => 'The room type is required.',
            'room_type_id.exists' => 'The selected room type is invalid.',
            'room_number.required' => 'The room number is required.',
            'room_number.string' => 'The room number must be a string.',
            'room_number.max' => 'The room number must not exceed 50 characters.',
            'capacity.required' => 'The capacity is required.',
            'capacity.integer' => 'The capacity must be a whole number.',
            'capacity.min' => 'The capacity must be at least 1.',
            'rent.required' => 'The rent is required.',
            'rent.numeric' => 'The rent must be a number.',
            'rent.min' => 'The rent must be at least 0.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be available, occupied, or maintenance.',
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
            'hostel_id' => 'hostel',
            'room_type_id' => 'room type',
            'room_number' => 'room number',
            'capacity' => 'capacity',
            'rent' => 'rent',
            'status' => 'status',
        ];
    }
}
