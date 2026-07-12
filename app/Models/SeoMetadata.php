<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoMetadata extends Model
{
    use HasFactory;

    protected $table = 'seo_metadata';

    protected $fillable = [
        'seoable_id',
        'seoable_type',
        'meta_fields',
        'twitter_fields',
        'og_fields',
        'schema_fields',
    ];

    protected $casts = [
        'meta_fields' => 'array',
        'twitter_fields' => 'array',
        'og_fields' => 'array',
        'schema_fields' => 'array',
    ];

    public function seoable()
    {
        return $this->morphTo();
    }
}
