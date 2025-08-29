<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'price',
        'original_price',
        'discount_amount',
        'discount_percentage',
        'quantity',
        'subtotal',
        'size',
        'color',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_price',
        'formatted_original_price',
        'formatted_discount_amount',
        'formatted_subtotal',
        'formatted_original_subtotal',
        'has_discount',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_id', 'product_id')
            ->where('size', $this->size)
            ->where('color', $this->color);
    }

    // Helper methods
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedOriginalPriceAttribute(): string
    {
        return '$' . number_format($this->original_price, 2);
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return '$' . number_format($this->discount_amount, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '$' . number_format($this->subtotal, 2);
    }

    public function getFormattedOriginalSubtotalAttribute(): string
    {
        return '$' . number_format($this->original_price * $this->quantity, 2);
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_amount > 0 || $this->discount_percentage > 0;
    }
}
