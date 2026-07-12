<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $roleId = $this->route('role')?->id;

        return [
            'name' => [
                $roleId ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'permissions'        => ['nullable', 'array'],
            'permissions.*'      => ['string', Rule::exists('permissions', 'name')],
            'update_permissions' => ['nullable', 'boolean'],
            'is_active'          => ['nullable', 'boolean'],
        ];
    }
}
