<?php

namespace App\Http\Requests;

use App\Models\ProductTag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $tagId = $this->route('attribute')?->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('product_tags', 'name')->ignore($tagId)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('product_tags', 'slug')->ignore($tagId)],
            'type' => ['required', Rule::in(array_keys(ProductTag::TYPES))],
            'option' => ['nullable', Rule::in(array_keys(ProductTag::OPTIONS))],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => filter_var($this->input('is_active', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
