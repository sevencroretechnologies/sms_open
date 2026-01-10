<?php

namespace App\Http\Requests;

class StudentCategoryStoreRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-student-categories');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:student_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    protected function customMessages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.max' => 'The category name must not exceed 100 characters.',
            'name.unique' => 'A category with this name already exists.',
            'description.max' => 'The description must not exceed 500 characters.',
        ];
    }

    protected function customAttributes(): array
    {
        return [
            'name' => 'category name',
            'is_active' => 'active status',
        ];
    }
}
