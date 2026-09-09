<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'is_exchange',
        'exchange_from_order_id',
        'payment_method',
        'subtotal',
        'shipping_fee',
        'shipping_fee_refunded',
        'total',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'is_exchange' => 'boolean',
            'shipping_fee_refunded' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function exchangeFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'exchange_from_order_id');
    }
}