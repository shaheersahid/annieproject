<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'items',
        'cart_total',
        'abandoned_at',
        'recovered_at',
    ];

    protected $casts = [
        'items' => 'array',
        'abandoned_at' => 'datetime',
        'recovered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

