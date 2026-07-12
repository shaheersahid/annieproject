<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $sellerId = $this->route('seller')?->id;

        return [
            'store_logo' => ['nullable', 'image', 'max:4096'],
            'cover_photo' => ['nullable', 'image', 'max:4096'],
            'username' => ['required', 'string', 'max:100', Rule::unique('sellers', 'username')->ignore($sellerId)],
            'store_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('sellers', 'email')->ignore($sellerId)],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['required', Rule::in(['Pakistan', 'PK'])],
            'zip_code' => ['nullable', 'string', 'max:30'],
            'location' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $country = $this->input('country', 'Pakistan');

        $this->merge([
            'country' => strtoupper($country) === 'PK' ? 'Pakistan' : $country,
            'is_active' => filter_var($this->input('is_active', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
