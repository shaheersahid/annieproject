<?php

namespace App\Traits;

use App\Models\SeoMetadata;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo
{
    /**
     * Get the SEO metadata for the entity.
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }
    
    /**
     * Helper to get SEO title or fallback to name
     */
    public function getSeoTitle(): string
    {
        return $this->seo?->meta_fields['title'] ?? $this->name ?? config('app.name');
    }

    /**
     * Helper to get SEO description or fallback
     */
    public function getSeoDescription(): string
    {
        return $this->seo?->meta_fields['description'] ?? $this->description ?? '';
    }
}
