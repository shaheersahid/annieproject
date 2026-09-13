<?php

namespace App\Traits;

use App\Models\SeoMetadata;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

trait HasSeo
{
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    public function seoField(string $group, string $key, mixed $fallback = null): mixed
    {
        $value = data_get($this->seo?->{$group}, $key);

        if (is_string($value)) {
            $value = trim($value);
        }

        return $value !== null && $value !== '' ? $value : $fallback;
    }

    public function getSeoTitle(): string
    {
        return (string) ($this->seoField('meta_fields', 'title')
            ?: $this->name
            ?: config('app.name'));
    }

    public function getSeoDescription(): string
    {
        $description = $this->seoField('meta_fields', 'description')
            ?: ($this->short_description ?? null)
            ?: ($this->description ?? '');

        $clean = trim(preg_replace('/\s+/', ' ', strip_tags((string) $description)) ?? '');

        return Str::limit($clean, 180);
    }

    public function getSeoKeywords(): string
    {
        $keywords = $this->seoField('meta_fields', 'keywords');

        if ($keywords) {
            return (string) $keywords;
        }

        if (method_exists($this, 'enabledTags')) {
            return $this->enabledTags()->pluck('name')->filter()->implode(', ');
        }

        return '';
    }

    public function getSeoCanonical(): string
    {
        return (string) ($this->seoField('meta_fields', 'canonical')
            ?: (method_exists($this, 'getSeoUrl') ? $this->getSeoUrl() : url()->current()));
    }

    public function getSeoRobots(): string
    {
        return (string) ($this->seoField('meta_fields', 'robots') ?: 'index, follow');
    }

    public function getSeoImage(): string
    {
        return (string) ($this->seoField('og_fields', 'image')
            ?: $this->primaryImage?->url
            ?: asset('assets/images/products/product-1.jpg'));
    }
}
