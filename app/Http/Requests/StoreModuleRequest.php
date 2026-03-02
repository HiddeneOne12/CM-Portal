<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return validatePermissions(\App\Http\Controllers\Admin\Acl\ModuleController::PERMISSION_ADD);
    }

    public function rules(): array
    {
        return [
            'module_category_id' => ['required', 'integer', 'exists:tbl_module_categories,id'],
            'module_name'        => ['required', 'string', 'max:255'],
            'route'              => ['required', 'string', 'max:255'],
            'css_class'          => ['nullable', 'string', 'max:255'],
            'show_in_menu_checkbox' => ['nullable'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_in_menu' => $this->has('show_in_menu_checkbox') ? 1 : 0,
        ]);
    }
}