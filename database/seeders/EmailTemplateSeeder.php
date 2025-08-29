<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('is_admin', true)->get();

        foreach ($users as $user) {
            // Welcome Email Template
            EmailTemplate::create([
                'user_id' => $user->id,
                'name' => 'Welcome Email',
                'subject' => 'Welcome to {{website_name}}, {{customer_name}}!',
                'content' => $this->getWelcomeEmailContent(),
                'type' => 'welcome',
                'variables' => ['customer_name', 'customer_email', 'website_name'],
                'is_active' => true,
            ]);

            // Sales Email Template
            EmailTemplate::create([
                'user_id' => $user->id,
                'name' => 'Sales Promotion',
                'subject' => 'Special Offer Just for You, {{customer_name}}!',
                'content' => $this->getSalesEmailContent(),
                'type' => 'sales',
                'variables' => ['customer_name', 'customer_email', 'discount_code', 'discount_amount', 'website_name'],
                'is_active' => true,
            ]);

            // Newsletter Template
            EmailTemplate::create([
                'user_id' => $user->id,
                'name' => 'Monthly Newsletter',
                'subject' => '{{website_name}} Newsletter - Latest Updates',
                'content' => $this->getNewsletterEmailContent(),
                'type' => 'newsletter',
                'variables' => ['customer_name', 'customer_email', 'website_name'],
                'is_active' => true,
            ]);

            // Abandoned Cart Template
            EmailTemplate::create([
                'user_id' => $user->id,
                'name' => 'Abandoned Cart Reminder',
                'subject' => 'Don\'t forget your cart, {{customer_name}}!',
                'content' => $this->getAbandonedCartEmailContent(),
                'type' => 'abandoned_cart',
                'variables' => ['customer_name', 'customer_email', 'order_number', 'order_total', 'website_name'],
                'is_active' => true,
            ]);
        }
    }

    private function getWelcomeEmailContent(): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Welcome to {{website_name}}</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #2563eb; margin-bottom: 10px;">Welcome to {{website_name}}!</h1>
                <p style="font-size: 18px; color: #666;">Hi {{customer_name}},</p>
            </div>

            <div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <p>Thank you for joining {{website_name}}! We\'re excited to have you as part of our community.</p>
                <p>Here\'s what you can expect from us:</p>
                <ul style="margin-left: 20px;">
                    <li>Exclusive deals and promotions</li>
                    <li>Latest product updates</li>
                    <li>Helpful tips and guides</li>
                    <li>Special member-only content</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="#" style="background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">Start Shopping</a>
            </div>

            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; text-align: center; font-size: 14px; color: #666;">
                <p>If you have any questions, feel free to contact us.</p>
                <p>Best regards,<br>The {{website_name}} Team</p>
                <p style="margin-top: 20px;">
                    <a href="{{unsubscribe_link}}" style="color: #666; text-decoration: underline;">Unsubscribe</a>
                </p>
            </div>
        </body>
        </html>';
    }

    private function getSalesEmailContent(): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Special Offer - {{website_name}}</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #dc2626; margin-bottom: 10px;">🎉 Special Offer Just for You!</h1>
                <p style="font-size: 18px; color: #666;">Hi {{customer_name}},</p>
            </div>

            <div style="background-color: #fef2f2; border: 2px solid #dc2626; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <h2 style="color: #dc2626; margin-bottom: 10px;">{{discount_amount}} OFF</h2>
                <p style="font-size: 16px; margin-bottom: 15px;">Use code: <strong>{{discount_code}}</strong></p>
                <p style="font-size: 14px; color: #666;">Limited time offer - Don\'t miss out!</p>
            </div>

            <div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3 style="color: #2563eb; margin-bottom: 15px;">Why shop with {{website_name}}?</h3>
                <ul style="margin-left: 20px;">
                    <li>Premium quality products</li>
                    <li>Fast and reliable shipping</li>
                    <li>Excellent customer service</li>
                    <li>Secure payment options</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="#" style="background-color: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;">Shop Now & Save</a>
            </div>

            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; text-align: center; font-size: 14px; color: #666;">
                <p>This offer expires soon, so don\'t wait!</p>
                <p>Best regards,<br>The {{website_name}} Team</p>
                <p style="margin-top: 20px;">
                    <a href="{{unsubscribe_link}}" style="color: #666; text-decoration: underline;">Unsubscribe</a>
                </p>
            </div>
        </body>
        </html>';
    }

    private function getNewsletterEmailContent(): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{{website_name}} Newsletter</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #2563eb; margin-bottom: 10px;">{{website_name}} Newsletter</h1>
                <p style="font-size: 18px; color: #666;">Hi {{customer_name}},</p>
            </div>

            <div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3 style="color: #2563eb; margin-bottom: 15px;">📰 What\'s New This Month</h3>
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #374151; margin-bottom: 8px;">🆕 New Products</h4>
                    <p>Discover our latest arrivals and trending items that everyone is talking about!</p>
                </div>
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #374151; margin-bottom: 8px;">🏆 Customer Spotlight</h4>
                    <p>See how our customers are using our products and get inspired!</p>
                </div>
                <div>
                    <h4 style="color: #374151; margin-bottom: 8px;">💡 Tips & Tricks</h4>
                    <p>Expert advice to help you get the most out of your purchases.</p>
                </div>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="#" style="background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">Read More</a>
            </div>

            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; text-align: center; font-size: 14px; color: #666;">
                <p>Thank you for being part of our community!</p>
                <p>Best regards,<br>The {{website_name}} Team</p>
                <p style="margin-top: 20px;">
                    <a href="{{unsubscribe_link}}" style="color: #666; text-decoration: underline;">Unsubscribe</a>
                </p>
            </div>
        </body>
        </html>';
    }

    private function getAbandonedCartEmailContent(): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Complete Your Order - {{website_name}}</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #f59e0b; margin-bottom: 10px;">🛒 Don\'t Forget Your Cart!</h1>
                <p style="font-size: 18px; color: #666;">Hi {{customer_name}},</p>
            </div>

            <div style="background-color: #fffbeb; border: 2px solid #f59e0b; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3 style="color: #f59e0b; margin-bottom: 15px;">Your items are waiting for you!</h3>
                <p>We noticed you left some items in your cart (Order #{{order_number}}) with a total value of <strong>{{order_total}}</strong>.</p>
                <p style="margin-top: 15px;">Complete your purchase now before these items sell out!</p>
            </div>

            <div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h4 style="color: #2563eb; margin-bottom: 10px;">Why complete your order?</h4>
                <ul style="margin-left: 20px;">
                    <li>Secure your items before they sell out</li>
                    <li>Fast and secure checkout process</li>
                    <li>Free shipping on orders over $50</li>
                    <li>30-day money-back guarantee</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="#" style="background-color: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;">Complete Your Order</a>
            </div>

            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; text-align: center; font-size: 14px; color: #666;">
                <p>Your cart will be saved for 24 hours.</p>
                <p>Best regards,<br>The {{website_name}} Team</p>
                <p style="margin-top: 20px;">
                    <a href="{{unsubscribe_link}}" style="color: #666; text-decoration: underline;">Unsubscribe</a>
                </p>
            </div>
        </body>
        </html>';
    }
}
