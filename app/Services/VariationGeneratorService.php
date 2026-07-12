<?php

namespace App\Services;

use App\Contracts\VariationGeneratorServiceInterface;
use App\Models\Product;

class VariationGeneratorService implements VariationGeneratorServiceInterface
{
    public function generateCombinations(array $attributeValueMap): array
    {
        if (empty($attributeValueMap)) {
            return [];
        }

        $combinations = [[]];
        foreach ($attributeValueMap as $attributeSlug => $values) {
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($values as $value) {
                    $newCombinations[] = array_merge($combination, [
                        $attributeSlug => $value,
                    ]);
                }
            }
            $combinations = $newCombinations;
        }

        return $combinations;
    }

    public function syncProductVariants(Product $product, array $combinations, bool $safeMode = true): array
    {
        // Phase 2.1 safety: no writes to core runtime flow.
        return [
            'created' => 0,
            'updated' => 0,
            'skipped' => count($combinations),
            'safe_mode' => $safeMode,
            'product_id' => $product->id,
        ];
    }

    public function buildCombinationHash(array $resolvedItems): string
    {
        ksort($resolvedItems);
        return hash('sha256', json_encode($resolvedItems));
    }

    public function resolveLegacyAttributesToIds(array $legacyAttributes): array
    {
        // Phase 2.1 safety: mapping will be activated in Phase 2.2.
        return $legacyAttributes;
    }
}
