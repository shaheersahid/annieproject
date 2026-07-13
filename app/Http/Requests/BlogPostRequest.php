<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $date = $this->string('publish_date')->trim()->toString();
        $time = $this->string('publish_time')->trim()->toString();

        $this->merge([
            'slug' => $this->filled('slug') ? $this->string('slug')->slug()->toString() : null,
            'published_at' => $date !== ''
                ? $date.' '.($time ?: '00:00').':00'
                : null,
        ]);
    }

    public function rules(): array
    {
        $postId = $this->route('blog')?->id ?? 'NULL';

        return [
            'title'            => 'required|string|max:255',
            'slug'             => "nullable|string|max:255|unique:blog_posts,slug,{$postId}",
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'required|string',
            'featured_image'   => 'nullable|image|max:2048',
            'status'           => 'required|in:draft,published',
            'publish_date'     => 'nullable|required_with:publish_time|date_format:Y-m-d',
            'publish_time'     => 'nullable|required_with:publish_date|date_format:H:i',
            'published_at'     => 'nullable|date_format:Y-m-d H:i:s',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Post content is required.',
            'publish_date.required_with' => 'Select publish date.',
            'publish_time.required_with' => 'Select publish time.',
        ];
    }
}
