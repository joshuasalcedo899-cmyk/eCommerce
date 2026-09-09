<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Product;

class OrderItemReturn extends Model
{
    protected $fillable = [
        'order_item_id',
        'replacement_product_id',
        'replacement_size',
        'user_id',
        'type',
        'quantity',
        'reason',
        'status',
        'admin_note',
        'replacement_subtotal',
        'price_difference',
        'settlement_method',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
    ];

    protected function casts(): array
    {
        return [
            'replacement_subtotal' => 'decimal:2',
            'price_difference' => 'decimal:2',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replacementProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'replacement_product_id');
    }

    public function replacementItems(): HasMany
    {
        return $this->hasMany(ExchangeRequestItem::class, 'order_item_return_id');
    }
}
