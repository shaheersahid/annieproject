<?php

return [
    'attribute_system_enabled' => env('FEATURE_ATTRIBUTE_SYSTEM', false),
    'variation_system_enabled' => env('FEATURE_VARIATION_SYSTEM', false),
    'pricing_pipeline_enabled' => env('FEATURE_PRICING_PIPELINE', false),
    'variation_backfill_enabled' => env('FEATURE_VARIATION_BACKFILL', false),
    'write_legacy_variant_attributes_json' => env('FEATURE_WRITE_LEGACY_VARIANT_JSON', true),
    'read_legacy_variant_attributes_json_fallback' => env('FEATURE_READ_LEGACY_VARIANT_JSON_FALLBACK', true),
];
