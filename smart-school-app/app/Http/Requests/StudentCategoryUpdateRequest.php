<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StudentCategoryUpdateRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit-student-categories');
    }

    public function rules(): array
    {
        $categoryId = $this->route('studentCategory');
        $id = is_object($categoryId) ? $categoryId->id : $categoryId;

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('student_categories', 'name')->ignore($id)],
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
