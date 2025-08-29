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
        'images',
        'sku',
        'stock',
        'price_adjustment',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'stock' => 'integer',
        'price_adjustment' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'images' => 'array',
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

    // Image handling methods
    public function getMainImageAttribute(): string
    {
        if ($this->images && count($this->images) > 0) {
            return asset('storage/' . $this->images[0]);
        }
        return asset('images/placeholder-product.jpg');
    }

    public function getImageUrlsAttribute(): array
    {
        if ($this->images && count($this->images) > 0) {
            return array_map(function($image) {
                return asset('storage/' . $image);
            }, $this->images);
        }
        return [asset('images/placeholder-product.jpg')];
    }

    public function getHasImagesAttribute(): bool
    {
        return $this->images && count($this->images) > 0;
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

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
