<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;

trait HasImages
{
    /**
     * Get all of the entity's images.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('order');
    }

    /**
     * Get the entity's primary image.
     */
    public function primaryImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'primary');
    }

    /**
     * Get the URL of the primary image or a fallback.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        return $this->primaryImage?->url ?? asset('assets/img/placeholder.png');
    }

    /**
     * Sync images for the entity.
     */
    public function syncImages(array $imagePaths, ?string $primaryPath = null)
    {
        // For simple implementations where we just pass paths
        foreach ($imagePaths as $path) {
            $type = $path === $primaryPath ? 'primary' : 'gallery';
            $this->images()->updateOrCreate(
                ['path' => $path],
                ['type' => $type]
            );
        }

        // Clean up images no longer in the list
        $this->images()->whereNotIn('path', $imagePaths)->delete();
    }
}
