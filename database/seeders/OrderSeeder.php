<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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

        // Get existing products
        $nikeProduct = Product::where('sku', 'NIKE-AM270-001')->first();
        $adidasProduct = Product::where('sku', 'ADIDAS-UB22-001')->first();
        $uaProduct = Product::where('sku', 'UA-HOVR-001')->first();

        // If products don't exist, create them
        if (!$nikeProduct || !$adidasProduct || !$uaProduct) {
            $basketballCategory = Category::where('name', 'Basketball')->first();
            $runningCategory = Category::where('name', 'Running')->first();
            $nikeBrand = Brand::where('name', 'Nike')->first();
            $adidasBrand = Brand::where('name', 'Adidas')->first();
            $underArmourBrand = Brand::where('name', 'Under Armour')->first();

            if (!$nikeProduct) {
                $nikeProduct = Product::create([
                    'name' => 'Nike Air Max 270',
                    'description' => 'Premium running shoes with Air Max technology',
                    'price' => 149.99,
                    'images' => ['https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop'],
                    'category_id' => $runningCategory->id,
                    'brand_id' => $nikeBrand->id,
                    'sku' => 'NIKE-AM270-001',
                    'is_active' => true,
                    'is_featured' => true,
                ]);
            }

            if (!$adidasProduct) {
                $adidasProduct = Product::create([
                    'name' => 'Adidas Ultraboost 22',
                    'description' => 'High-performance running shoes with Boost technology',
                    'price' => 199.98,
                    'images' => ['https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=400&fit=crop'],
                    'category_id' => $runningCategory->id,
                    'brand_id' => $adidasBrand->id,
                    'sku' => 'ADIDAS-UB22-001',
                    'is_active' => true,
                    'is_featured' => true,
                ]);
            }

            if (!$uaProduct) {
                $uaProduct = Product::create([
                    'name' => 'Under Armour HOVR',
                    'description' => 'Comfortable training shoes with HOVR cushioning',
                    'price' => 149.99,
                    'images' => ['https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=400&fit=crop'],
                    'category_id' => $basketballCategory->id,
                    'brand_id' => $underArmourBrand->id,
                    'sku' => 'UA-HOVR-001',
                    'is_active' => true,
                    'is_featured' => false,
                ]);
            }
        }

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
                        'product_id' => $uaProduct->id,
                        'quantity' => 3,
                        'size' => '11',
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

                // Add some discounts to make it interesting
                $originalPrice = $product->price;
                $discountPercentage = 0;
                $discountAmount = 0;
                $finalPrice = $originalPrice;

                // Apply discounts to some items
                if ($product->sku === 'NIKE-AM270-001') {
                    // 20% discount on Nike shoes
                    $discountPercentage = 20.00;
                    $discountAmount = $originalPrice * ($discountPercentage / 100);
                    $finalPrice = $originalPrice - $discountAmount;
                } elseif ($product->sku === 'ADIDAS-UB22-001') {
                    // 15% discount on Adidas shoes
                    $discountPercentage = 15.00;
                    $discountAmount = $originalPrice * ($discountPercentage / 100);
                    $finalPrice = $originalPrice - $discountAmount;
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
