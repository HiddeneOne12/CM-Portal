<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return validatePermissions(\App\Http\Controllers\Admin\Acl\ModuleCategoryController::PERMISSION_EDIT);
    }

    public function rules(): array
    {
        $id = decryptIdFromUrl($this->route('token'));

        return [
            'category_name' => [
                'required',
                'string',
                'max:255',
                'unique:tbl_module_categories,category_name,' . $id
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_name.required' => 'Category name is required.',
            'category_name.unique'   => 'Category name already exists.',
        ];
    }
}