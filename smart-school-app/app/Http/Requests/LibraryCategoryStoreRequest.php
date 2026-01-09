<?php

namespace App\Http\Requests;

/**
 * Library Category Store Request
 * 
 * Prompt 369: Create Library Category Store Form Request
 * 
 * Validates library category data.
 */
class LibraryCategoryStoreRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-library-categories');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Category Name
            'name' => ['required', 'string', 'max:100'],
            
            // Category Code (optional, unique)
            'code' => ['nullable', 'string', 'max:50', 'unique:library_categories,code'],
            
            // Description
            'description' => ['nullable', 'string', 'max:500'],
            
            // Active Status
            'is_active' => ['boolean'],
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
            'name.required' => 'The category name is required.',
            'name.string' => 'The category name must be a string.',
            'name.max' => 'The category name must not exceed 100 characters.',
            'code.string' => 'The category code must be a string.',
            'code.max' => 'The category code must not exceed 50 characters.',
            'code.unique' => 'This category code has already been taken.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description must not exceed 500 characters.',
            'is_active.boolean' => 'The active status must be true or false.',
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
            'name' => 'category name',
            'code' => 'category code',
            'description' => 'description',
            'is_active' => 'active status',
        ];
    }
}
