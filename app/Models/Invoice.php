<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'notes',
        'terms_conditions',
        'payment_method',
        'payment_reference',
        'paid_at',
        'sent_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'formatted_subtotal',
        'formatted_tax',
        'formatted_shipping',
        'formatted_discount',
        'formatted_total',
        'is_overdue',
        'days_overdue',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Helper methods
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'paid' => 'green',
            'overdue' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
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

    public function getFormattedDiscountAttribute(): string
    {
        return '$' . number_format($this->discount, 2);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total, 2);
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'paid' || $this->status === 'cancelled') {
            return false;
        }

        return $this->due_date && $this->due_date->isPast();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }

        return $this->due_date->diffInDays(now());
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Boot method to generate invoice number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT);
            }

            if (empty($invoice->invoice_date)) {
                $invoice->invoice_date = now();
            }
        });
    }
}
