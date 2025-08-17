<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'sku',
        'stock',
        'price_adjustment',
        'is_active',
    ];

    protected $casts = [
        'stock' => 'integer',
        'price_adjustment' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Helper methods
    public function getFinalPriceAttribute(): float
    {
        return $this->product->price + $this->price_adjustment;
    }

    public function getFormattedFinalPriceAttribute(): string
    {
        return '$' . number_format($this->final_price, 2);
    }

    public function getFormattedPriceAdjustmentAttribute(): string
    {
        if ($this->price_adjustment == 0) {
            return '';
        }

        $sign = $this->price_adjustment > 0 ? '+' : '';
        return $sign . '$' . number_format($this->price_adjustment, 2);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = [];

        if ($this->size) {
            $parts[] = $this->size;
        }

        if ($this->color) {
            $parts[] = $this->color;
        }

        return implode(' - ', $parts) ?: 'Default';
    }

    // Scopes
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
