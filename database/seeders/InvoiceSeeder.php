<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            // Create an invoice for each order
            Invoice::create([
                'order_id' => $order->id,
                'invoice_date' => now(),
                'due_date' => now()->addDays(30),
                'status' => fake()->randomElement(['draft', 'sent', 'paid']),
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'shipping' => $order->shipping,
                'discount' => $order->total_discount,
                'total' => $order->total,
                'notes' => fake()->optional()->sentence(),
                'terms_conditions' => 'Payment is due within 30 days of invoice date. Late payments may incur additional charges.',
                'payment_method' => fake()->optional()->randomElement(['Credit Card', 'Bank Transfer', 'PayPal']),
                'payment_reference' => fake()->optional()->uuid(),
                'paid_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
                'sent_at' => fake()->optional()->dateTimeBetween('-2 months', 'now'),
            ]);
        }
    }
}
