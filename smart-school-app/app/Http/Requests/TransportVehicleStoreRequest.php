<?php

namespace App\Http\Requests;

/**
 * Transport Vehicle Store Request
 * 
 * Prompt 371: Create Transport Vehicle Store Form Request
 * 
 * Validates transport vehicle data.
 */
class TransportVehicleStoreRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-transport-vehicles');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Vehicle Number (unique)
            'vehicle_number' => ['required', 'string', 'max:50', 'unique:transport_vehicles,vehicle_number'],
            
            // Capacity
            'capacity' => ['required', 'integer', 'min:1'],
            
            // Driver ID (optional)
            'driver_id' => ['nullable', 'exists:users,id'],
            
            // Route ID (optional)
            'route_id' => ['nullable', 'exists:transport_routes,id'],
            
            // Insurance Expiry Date (optional)
            'insurance_expiry' => ['nullable', 'date'],
            
            // Documents (optional file upload)
            'documents' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
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
            'vehicle_number.required' => 'The vehicle number is required.',
            'vehicle_number.string' => 'The vehicle number must be a string.',
            'vehicle_number.max' => 'The vehicle number must not exceed 50 characters.',
            'vehicle_number.unique' => 'This vehicle number has already been registered.',
            'capacity.required' => 'The capacity is required.',
            'capacity.integer' => 'The capacity must be a whole number.',
            'capacity.min' => 'The capacity must be at least 1.',
            'driver_id.exists' => 'The selected driver is invalid.',
            'route_id.exists' => 'The selected route is invalid.',
            'insurance_expiry.date' => 'Please enter a valid insurance expiry date.',
            'documents.file' => 'The documents must be a file.',
            'documents.mimes' => 'The documents must be a PDF, JPG, JPEG, or PNG file.',
            'documents.max' => 'The documents must not exceed 5MB.',
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
            'vehicle_number' => 'vehicle number',
            'capacity' => 'capacity',
            'driver_id' => 'driver',
            'route_id' => 'route',
            'insurance_expiry' => 'insurance expiry date',
            'documents' => 'documents',
        ];
    }
}
