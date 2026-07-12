<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait AutoGeneratesSlug
{
    /**
     * Boot the trait.
     */
    public static function bootAutoGeneratesSlug()
    {
        static::saving(function (Model $model) {
            if (empty($model->slug)) {
                $source = $model->getAttribute($model->getSlugSourceColumn());
                if ($source) {
                    $model->slug = generateSlug($model, $source);
                }
            }
        });
    }

    /**
     * Get the column to generate the slug from.
     * Default to 'name'
     */
    public function getSlugSourceColumn(): string
    {
        return 'name';
    }
}
