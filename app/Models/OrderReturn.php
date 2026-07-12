<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'return_number',
        'status',
        'refund_amount',
        'reason',
        'requested_at',
    ];

    protected $casts = ['requested_at' => 'datetime'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

