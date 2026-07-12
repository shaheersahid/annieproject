<?php

namespace App\Http\Requests;

use App\Enums\ProductTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;
        $hasVariants = filter_var($this->input('has_variants', false), FILTER_VALIDATE_BOOLEAN);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'product_type' => ['required', Rule::in(array_column(ProductTypeEnum::cases(), 'value'))],
            'affiliate_platform' => ['nullable', Rule::in(['none', 'amazon', 'temu', 'both'])],
            'amazon_url' => ['nullable', 'url', 'max:2048'],
            'temu_url' => ['nullable', 'url', 'max:2048'],
            'external_product_id' => ['nullable', 'string', 'max:255'],
            'price_note' => ['nullable', 'string', 'max:255'],
            'affiliate_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'pros' => ['nullable', 'array'],
            'pros.*' => ['nullable', 'string', 'max:255'],
            'cons' => ['nullable', 'array'],
            'cons.*' => ['nullable', 'string', 'max:255'],
            'has_variants' => ['required', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'review_status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])],
            'is_draft' => ['nullable', 'boolean'],
            'out_of_stock' => ['nullable', 'boolean'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'seller_id' => ['nullable', 'exists:sellers,id'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:product_tags,id'],
            'size_chart_id' => ['nullable', 'exists:size_charts,id'],
            'discount_type' => ['nullable', Rule::in(['fixed', 'percentage'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_deal' => ['nullable', 'boolean'],
            'deal_enabled' => ['nullable', 'boolean'],
            'deal_type' => ['nullable', Rule::in(['fixed', 'percentage'])],
            'deal_value' => ['nullable', 'numeric', 'min:0'],
            'deal_start_at' => ['nullable', 'date'],
            'deal_end_at' => ['nullable', 'date', 'after_or_equal:deal_start_at'],
            'sold_out' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:4096'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/mpeg,video/quicktime,video/webm', 'max:51200'],
            'deleted_images' => ['nullable', 'array'],
            'deleted_images.*' => ['string'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'variants.*.attribute_id' => ['nullable', 'integer', 'exists:product_attributes,id'],
            'variants.*.value' => ['nullable', 'string', 'max:255'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.discount_type' => ['nullable', Rule::in(['fixed', 'percentage'])],
            'variants.*.discount_value' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'variants.*.image_path' => ['nullable', 'string', 'max:255'],
            'variants.*.deal_enabled' => ['nullable', 'boolean'],
            'variants.*.deal_type' => ['nullable', Rule::in(['fixed', 'percentage'])],
            'variants.*.deal_value' => ['nullable', 'numeric', 'min:0'],
            'variants.*.deal_start_at' => ['nullable', 'date'],
            'variants.*.deal_end_at' => ['nullable', 'date', 'after_or_equal:variants.*.deal_start_at'],
            'variants.*.attributes' => ['nullable'],
        ];

        if ($hasVariants) {
            $rules['base_price'] = ['nullable', 'numeric', 'min:0'];
            $rules['sale_price'] = ['nullable', 'numeric', 'min:0'];
            $rules['stock'] = ['nullable', 'integer', 'min:0'];
        } else {
            $rules['base_price'] = ['required', 'numeric', 'min:0'];
            $rules['sale_price'] = ['nullable', 'numeric', 'min:0', 'lt:base_price'];
            $rules['stock'] = ['nullable', 'integer', 'min:0'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $specifications = $this->input('specifications');
        if ($specifications === null && $this->has('specs')) {
            $specifications = $this->input('specs');
        }

        $isDraft = filter_var($this->input('is_draft', false), FILTER_VALIDATE_BOOLEAN);
        $isActive = filter_var($this->input('is_active', true), FILTER_VALIDATE_BOOLEAN);
        $status = $isDraft ? 'draft' : ($isActive ? 'published' : 'draft');

        $this->merge([
            'has_variants' => filter_var($this->input('has_variants', false), FILTER_VALIDATE_BOOLEAN),
            'product_type' => $this->input('product_type', $this->input('type', 'frame')),
            'is_deal' => filter_var($this->input('is_deal', false), FILTER_VALIDATE_BOOLEAN),
            'deal_enabled' => filter_var($this->input('deal_enabled', $this->input('is_deal', false)), FILTER_VALIDATE_BOOLEAN),
            'base_price' => $this->input('base_price', $this->input('price')),
            'price_note' => $this->input('price_note', 'Check latest price'),
            'affiliate_rating' => $this->input('affiliate_rating'),
            'pros' => $this->linesToArray($this->input('pros')),
            'cons' => $this->linesToArray($this->input('cons')),
            'stock' => $this->input('stock', $this->input('stock_quantity', 0)),
            'low_stock_threshold' => $this->input('low_stock_threshold', 5),
            'specifications' => $specifications,
            'is_draft' => $isDraft,
            'is_active' => $isActive,
            'is_featured' => filter_var($this->input('is_featured', false), FILTER_VALIDATE_BOOLEAN),
            'affiliate_platform' => $this->input('affiliate_platform', 'none'),
            'out_of_stock' => filter_var($this->input('out_of_stock', false), FILTER_VALIDATE_BOOLEAN),
            'status' => $status,
        ]);
    }

    private function linesToArray(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    public function messages(): array
    {
        return [
            'category_ids.required' => 'Please select at least one category.',
            'product_type.in' => 'Selected optical product type is invalid.',
        ];
    }
}
