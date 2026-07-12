<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait AutoGeneratesSku
{
    /**
     * Boot the trait.
     */
    public static function bootAutoGeneratesSku()
    {
        static::creating(function (Model $model) {
            if (empty($model->sku)) {
                $model->sku = generateSku($model, $model->getSkuPrefix());
            }
        });
    }

    /**
     * Get the prefix for the SKU.
     * Default to 'ITEM'
     */
    public function getSkuPrefix(): string
    {
        return 'ITEM';
    }
}
