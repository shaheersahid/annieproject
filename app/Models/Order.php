<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'seller_id',
        'status',
        'sub_total',
        'vat_total',
        'discount_total',
        'shipping_total',
        'tax_total',
        'grand_total',
        'payment_status',
        'shipping_address',
        'notes',
        'ordered_at',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class);
    }
}
