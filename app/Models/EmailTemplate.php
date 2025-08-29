<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'subject',
        'content',
        'type',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class);
    }

    public function getAvailableVariables(): array
    {
        return [
            'customer_name' => 'Customer Name',
            'customer_email' => 'Customer Email',
            'customer_phone' => 'Customer Phone',
            'customer_address' => 'Customer Address',
            'order_number' => 'Order Number',
            'order_total' => 'Order Total',
            'order_date' => 'Order Date',
            'product_name' => 'Product Name',
            'product_price' => 'Product Price',
            'discount_code' => 'Discount Code',
            'discount_amount' => 'Discount Amount',
            'website_name' => 'Website Name',
            'company_name' => 'Company Name',
            'unsubscribe_link' => 'Unsubscribe Link',
        ];
    }

    public function replaceVariables(string $content, array $data = []): string
    {
        $variables = $this->getAvailableVariables();

        foreach ($variables as $key => $label) {
            $value = $data[$key] ?? '';
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }

        return $content;
    }
}
