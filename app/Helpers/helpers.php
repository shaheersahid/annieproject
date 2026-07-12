<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('resolve_image_path')) {
    /**
     * Resolve the URL of an image path.
     *
     * @param string|null $path
     * @return string|null
     */
    function resolve_image_path(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // If the path is already a full URL, return it
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // If the path starts with public assets folders, resolve it using asset()
        if (str_starts_with($path, 'assets/') || str_starts_with($path, 'admin/')) {
            return asset($path);
        }

        // If the path already has a storage prefix (e.g. storage/products/...)
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        // If it starts with a slash, we can clean it up
        if (str_starts_with($path, '/')) {
            $path = ltrim($path, '/');
            if (str_starts_with($path, 'storage/')) {
                return asset($path);
            }
            if (str_starts_with($path, 'assets/') || str_starts_with($path, 'admin/')) {
                return asset($path);
            }
        }

        // Default to storage public disk
        return Storage::disk('public')->url($path);
    }
}
