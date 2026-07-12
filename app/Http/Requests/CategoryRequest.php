<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name'         => ['required', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'parent_id'    => ['nullable', 'exists:categories,id'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
            'show_on_home' => ['nullable', 'boolean'],
            'description'  => ['nullable', 'string'],
            'image'        => ['nullable', 'image', 'max:2048'],
        ];
    }
}
