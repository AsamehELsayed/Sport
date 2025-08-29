<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Get existing products with variants
        $nikeProduct = Product::where('sku', 'NK-AM270-001')->first();
        $adidasProduct = Product::where('sku', 'AD-UB22-001')->first();
        $wilsonProduct = Product::where('sku', 'WL-EVO-001')->first();

        // If products don't exist, create them with variants
        if (!$nikeProduct || !$adidasProduct || !$wilsonProduct) {
            $runningCategory = Category::where('name', 'Running')->first();
            $basketballCategory = Category::where('name', 'Basketball')->first();
            $nikeBrand = Brand::where('name', 'Nike')->first();
            $adidasBrand = Brand::where('name', 'Adidas')->first();
            $wilsonBrand = Brand::where('name', 'Wilson')->first();

            if (!$nikeProduct) {
                $nikeProduct = Product::create([
                    'name' => 'Nike Air Max 270',
                    'description' => 'Premium running shoes with Air Max technology',
                    'price' => 149.99,
                    'category_id' => $runningCategory->id,
                    'brand_id' => $nikeBrand->id,
                    'sku' => 'NK-AM270-001',
                    'is_active' => true,
                    'is_featured' => true,
                ]);

                // Create variants for Nike product
                $nikeProduct->variants()->createMany([
                    [
                        'size' => '9',
                        'color' => 'Pure White',
                        'stock' => 20,
                        'sku' => 'NK-AM270-001-9-Pure-White',
                        'price_adjustment' => 0,
                        'is_active' => true,
                        'is_default' => true,
                    ],
                    [
                        'size' => '10',
                        'color' => 'Pure White',
                        'stock' => 18,
                        'sku' => 'NK-AM270-001-10-Pure-White',
                        'price_adjustment' => 0,
                        'is_active' => true,
                        'is_default' => false,
                    ],
                    [
                        'size' => '9',
                        'color' => 'Crimson',
                        'stock' => 15,
                        'sku' => 'NK-AM270-001-9-Crimson',
                        'price_adjustment' => 0,
                        'is_active' => true,
                        'is_default' => false,
                    ]
                ]);
            }

            if (!$adidasProduct) {
                $adidasProduct = Product::create([
                    'name' => 'Adidas Ultraboost 22',
                    'description' => 'High-performance running shoes with Boost technology',
                    'price' => 189.99,
                    'category_id' => $runningCategory->id,
                    'brand_id' => $adidasBrand->id,
                    'sku' => 'AD-UB22-001',
                    'is_active' => true,
                    'is_featured' => true,
                ]);

                // Create variants for Adidas product
                $adidasProduct->variants()->createMany([
                    [
                        'size' => '9',
                        'color' => 'Classic Black',
                        'stock' => 16,
                        'sku' => 'AD-UB22-001-9-Classic-Black',
                        'price_adjustment' => 0,
                        'is_active' => true,
                        'is_default' => true,
                    ],
                    [
                        'size' => '10',
                        'color' => 'Classic Black',
                        'stock' => 19,
                        'sku' => 'AD-UB22-001-10-Classic-Black',
                        'price_adjustment' => 0,
                        'is_active' => true,
                        'is_default' => false,
                    ]
                ]);
            }

            if (!$wilsonProduct) {
                $wilsonProduct = Product::create([
                    'name' => 'Wilson Evolution Basketball',
                    'description' => 'Official game ball with premium composite leather',
                    'price' => 64.99,
                    'category_id' => $basketballCategory->id,
                    'brand_id' => $wilsonBrand->id,
                    'sku' => 'WL-EVO-001',
                    'is_active' => true,
                    'is_featured' => false,
                ]);

                // Create variants for Wilson product
                $wilsonProduct->variants()->createMany([
                    [
                        'size' => 'Official Size',
                        'color' => 'Amber',
                        'stock' => 15,
                        'sku' => 'WL-EVO-001-Official-Size-Amber',
                        'price_adjustment' => 0,
                        'is_active' => true,
                        'is_default' => true,
                    ]
                ]);
            }
        }

        // Get variants for order items
        $nikeVariant = $nikeProduct->variants()->where('size', '10')->where('color', 'Pure White')->first();
        $adidasVariant = $adidasProduct->variants()->where('size', '9')->where('color', 'Classic Black')->first();
        $wilsonVariant = $wilsonProduct->variants()->first();

        // Create sample orders
        $orders = [
            [
                'order_number' => 'ORD-2025-000001',
                'status' => 'pending',
                'payment_status' => 'pending',
                'customer_name' => 'John Doe',
                'customer_email' => 'john@example.com',
                'customer_phone' => '+1234567890',
                'shipping_address_line_1' => '123 Main St',
                'shipping_city' => 'New York',
                'shipping_state' => 'NY',
                'shipping_postal_code' => '10001',
                'shipping_country' => 'United States',
                'payment_method' => 'credit_card',
                'tax' => 24.00,
                'shipping' => 10.00,
                'items' => [
                    [
                        'product_id' => $nikeProduct->id,
                        'quantity' => 2,
                        'size' => '10',
                        'color' => 'Pure White',
                    ]
                ]
            ],
            [
                'order_number' => 'ORD-2025-000002',
                'status' => 'processing',
                'payment_status' => 'paid',
                'customer_name' => 'Jane Smith',
                'customer_email' => 'jane@example.com',
                'customer_phone' => '+1987654321',
                'shipping_address_line_1' => '456 Oak Ave',
                'shipping_city' => 'Los Angeles',
                'shipping_state' => 'CA',
                'shipping_postal_code' => '90210',
                'shipping_country' => 'United States',
                'payment_method' => 'credit_card',
                'tax' => 16.00,
                'shipping' => 10.00,
                'items' => [
                    [
                        'product_id' => $adidasProduct->id,
                        'quantity' => 1,
                        'size' => '9',
                        'color' => 'Classic Black',
                    ]
                ]
            ],
            [
                'order_number' => 'ORD-2025-000003',
                'status' => 'shipped',
                'payment_status' => 'paid',
                'customer_name' => 'Mike Johnson',
                'customer_email' => 'mike@example.com',
                'customer_phone' => '+1122334455',
                'shipping_address_line_1' => '789 Pine St',
                'shipping_city' => 'Chicago',
                'shipping_state' => 'IL',
                'shipping_postal_code' => '60601',
                'shipping_country' => 'United States',
                'payment_method' => 'credit_card',
                'tax' => 36.00,
                'shipping' => 10.00,
                'items' => [
                    [
                        'product_id' => $wilsonProduct->id,
                        'quantity' => 3,
                        'size' => 'Official Size',
                        'color' => 'Amber',
                    ]
                ]
            ]
        ];

        foreach ($orders as $orderData) {
            $items = $orderData['items'];
            unset($orderData['items']);

            $order = Order::create(array_merge($orderData, [
                'user_id' => $user->id,
                'subtotal' => 0, // Will be updated after items are created
                'total' => 0, // Will be updated after items are created
            ]));

            $orderSubtotal = 0;

            foreach ($items as $itemData) {
                $product = Product::find($itemData['product_id']);

                // Find the specific variant
                $variant = $product->variants()
                    ->where('size', $itemData['size'])
                    ->where('color', $itemData['color'])
                    ->first();

                // Calculate final price with variant adjustment
                $originalPrice = $product->price;
                $variantAdjustment = $variant ? $variant->price_adjustment : 0;
                $basePrice = $originalPrice + $variantAdjustment;

                $discountPercentage = 0;
                $discountAmount = 0;
                $finalPrice = $basePrice;

                // Apply discounts to some items
                if ($product->sku === 'NK-AM270-001') {
                    // 20% discount on Nike shoes
                    $discountPercentage = 20.00;
                    $discountAmount = $basePrice * ($discountPercentage / 100);
                    $finalPrice = $basePrice - $discountAmount;
                } elseif ($product->sku === 'AD-UB22-001') {
                    // 15% discount on Adidas shoes
                    $discountPercentage = 15.00;
                    $discountAmount = $basePrice * ($discountPercentage / 100);
                    $finalPrice = $basePrice - $discountAmount;
                }

                $itemSubtotal = $finalPrice * $itemData['quantity'];
                $orderSubtotal += $itemSubtotal;

                OrderItem::create(array_merge($itemData, [
                    'order_id' => $order->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'price' => $finalPrice,
                    'original_price' => $originalPrice,
                    'discount_amount' => $discountAmount,
                    'discount_percentage' => $discountPercentage,
                    'subtotal' => $itemSubtotal,
                ]));
            }

            // Update order with calculated totals
            $order->update([
                'subtotal' => $orderSubtotal,
                'total' => $orderSubtotal + $order->tax + $order->shipping,
            ]);
        }
    }
}
