<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\Order;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index()
    {
        // Get featured products
        $featuredProducts = Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Get popular categories
        $popularCategories = Category::withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true)
            ->orderBy('products_count', 'desc')
            ->limit(6)
            ->get();

        // Get top brands
        $topBrands = Brand::withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true)
            ->orderBy('products_count', 'desc')
            ->limit(4)
            ->get();

        // Get latest products
        $latestProducts = Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Get sale products
        $saleProducts = Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true)
            ->where('discount', '>', 0)
            ->orderBy('discount', 'desc')
            ->limit(4)
            ->get();

        // Calculate statistics
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::where('is_active', true)->count(),
            'total_brands' => Brand::where('is_active', true)->count(),
            'total_customers' => User::where('is_admin', false)->count(),
            'total_orders' => Order::count(),
            'average_rating' => 4.8, // This would come from a reviews table in the future
        ];

        // Get website settings
        $websiteSettings = WebsiteSetting::getAllAsArray();

        return Inertia::render('Welcome', [
            'featuredProducts' => $featuredProducts,
            'popularCategories' => $popularCategories,
            'topBrands' => $topBrands,
            'latestProducts' => $latestProducts,
            'saleProducts' => $saleProducts,
            'stats' => $stats,
            'websiteSettings' => $websiteSettings,
        ]);
    }
}
