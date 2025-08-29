<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the customers that belong to this group.
     */
    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'customer_group_user')
            ->where('is_admin', false)
            ->withTimestamps();
    }

    /**
     * Get the customers count for this group.
     */
    public function getCustomersCountAttribute()
    {
        return $this->customers()->count();
    }

    /**
     * Scope to get only active groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
