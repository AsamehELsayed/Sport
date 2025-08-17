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
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'payment_method',
        'payment_status',
        'transaction_id',
        'notes',
        'admin_notes',
        'processed_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
        'processed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
        'payment_status_label',
        'formatted_total',
        'formatted_subtotal',
        'formatted_tax',
        'formatted_shipping',
        'formatted_original_subtotal',
        'formatted_total_discount',
        'has_discounts',
        'total_discount',
        'original_subtotal',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Helper methods
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'shipped' => 'purple',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'failed' => 'red',
            'refunded' => 'gray',
            default => 'gray',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return ucfirst($this->payment_status);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '$' . number_format($this->subtotal, 2);
    }

    public function getFormattedTaxAttribute(): string
    {
        return '$' . number_format($this->tax, 2);
    }

    public function getFormattedShippingAttribute(): string
    {
        return '$' . number_format($this->shipping, 2);
    }

    public function getTotalDiscountAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->discount_amount * $item->quantity;
        });
    }

    public function getOriginalSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->original_price * $item->quantity;
        });
    }

    public function getFormattedTotalDiscountAttribute(): string
    {
        return '$' . number_format($this->total_discount, 2);
    }

    public function getFormattedOriginalSubtotalAttribute(): string
    {
        return '$' . number_format($this->original_subtotal, 2);
    }

    public function getHasDiscountsAttribute(): bool
    {
        return $this->items->some(function ($item) {
            return $item->getHasDiscountAttribute();
        });
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Boot method to generate order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
