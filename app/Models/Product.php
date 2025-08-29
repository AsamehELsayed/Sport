<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'images',
        'category_id',
        'brand_id',
        'sku',
        'is_active',
        'is_featured',
        'discount',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    protected $appends = ['image_url' , 'main_image', 'image_urls', 'has_images', 'safe_main_image', 'safe_image_urls', 'final_price', 'formatted_final_price', 'formatted_price', 'discount_percentage', 'rating', 'reviews_count' , 'total_stock', 'has_stock', 'available_sizes', 'available_colors'];

    public function getImageUrlAttribute()
    {
        return $this->images[0] ?? null;
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true)->where('stock', '>', 0);
    }

    public function defaultVariant(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_default', true);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists');
    }

    // Helper methods for total stock
    public function getTotalStockAttribute(): int
    {
        return $this->variants()->sum('stock');
    }

    public function getHasStockAttribute(): bool
    {
        return $this->total_stock > 0;
    }

    public function getAvailableSizesAttribute(): array
    {
        return $this->variants()
            ->where('stock', '>', 0)
            ->pluck('size')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    public function getAvailableColorsAttribute(): array
    {
        return $this->variants()
            ->where('stock', '>', 0)
            ->pluck('color')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    // Image handling methods
    public function getMainImageAttribute(): string
    {
        $defaultVariant = $this->defaultVariant()->first();
        if ($defaultVariant && $defaultVariant->has_images) {
            return $defaultVariant->main_image;
        }

        // If no default variant with images, return placeholder
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
    /**
     * Get the images attribute as an array.
     *
     * @return array
     */
    public function getImagesAttribute($value): array
    {
        if (is_array($value)) {
            return array_map(function($image) {
                return asset('storage/' . ltrim($image, '/'));
            }, $value);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_map(function($image) {
                    return asset('storage/' . ltrim($image, '/'));
                }, $decoded);
            }
            // Fallback: comma-separated string
            $images = array_filter(array_map('trim', explode(',', $value)));
            return array_map(function($image) {
                return asset('storage/' . ltrim($image, '/'));
            }, $images);
        }

        return [];
    }
    // Safe image access methods
    public function getSafeMainImageAttribute(): string
    {
        if ($this->images && count($this->images) > 0) {
            $imagePath = storage_path('app/public/' . $this->images[0]);
            if (file_exists($imagePath)) {
                return asset('storage/' . $this->images[0]);
            }
        }
        return asset('images/placeholder-product.jpg');
    }

    public function getSafeImageUrlsAttribute(): array
    {
        if ($this->images && count($this->images) > 0) {
            $validImages = [];
            foreach ($this->images as $image) {
                $imagePath = storage_path('app/public/' . $image);
                if (file_exists($imagePath)) {
                    $validImages[] = asset('storage/' . $image);
                }
            }
            if (count($validImages) > 0) {
                return $validImages;
            }
        }
        return [asset('images/placeholder-product.jpg')];
    }

    // Price calculations
    public function getFinalPriceAttribute(): float
    {
        if ($this->discount > 0) {
            return round($this->price * (100 - $this->discount) / 100, 2);
        }
        return round($this->price, 2);
    }

    public function getFormattedFinalPriceAttribute(): string
    {
        return '$' . number_format($this->final_price, 2);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->discount > 0 && $this->price > 0) {
            return round(($this->discount / $this->price) * 100);
        }
        return 0;
    }

    // Rating and review methods (placeholder for future implementation)
    public function getRatingAttribute(): float
    {
        // This would typically come from a reviews table
        // For now, return a default rating
        return 4.5;
    }

    public function getReviewsCountAttribute(): int
    {
        // This would typically come from a reviews table
        // For now, return a default count
        return rand(50, 500);
    }
}
