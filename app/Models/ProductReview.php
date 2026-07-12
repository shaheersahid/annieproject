<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'reviewer_id', 
        'rating', 
        'review_text', 
        'reply_text', 
        'reply_by', 
        'status', 
        'reviewed_at'
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function replier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reply_by');
    }
}

