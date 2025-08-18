<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    private function getFilteredBrandCounts($filters = [])
    {
        $query = Product::where('is_active', true)
            ->where('discount', '>', 0);

        // Apply price range filters
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Apply search filter
        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Get brand counts based on filtered products
        $brandCounts = $query->selectRaw('brand_id, COUNT(*) as count')
            ->groupBy('brand_id')
            ->pluck('count', 'brand_id')
            ->toArray();

        // Get all active brands with their filtered counts
        $brands = Brand::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($brand) use ($brandCounts) {
                $brand->products_count = $brandCounts[$brand->id] ?? 0;
                return $brand;
            });

        return $brands;
    }

    private function getFilteredCategoryCounts($filters = [])
    {
        $query = Product::where('is_active', true)
            ->where('discount', '>', 0);

        // Apply price range filters
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Apply search filter
        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Get category counts based on filtered products
        $categoryCounts = $query->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->pluck('count', 'category_id')
            ->toArray();

        // Get all active categories with their filtered counts
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function ($category) use ($categoryCounts) {
                $category->products_count = $categoryCounts[$category->id] ?? 0;
                return $category;
            });

        return $categories;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true)
            ->where('discount', '>', 0);

        // Filter by brand (can be multiple brands)
        if ($request->has('brand')) {
            $brands = explode(',', $request->brand);
            $query->whereHas('brand', function ($q) use ($brands) {
                $q->whereIn('slug', $brands);
            });
        }

        // Filter by category (can be multiple categories)
        if ($request->has('category')) {
            $categories = explode(',', $request->category);
            $query->whereHas('category', function ($q) use ($categories) {
                $q->whereIn('slug', $categories);
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by discount percentage
        if ($request->has('min_discount')) {
            $query->where('discount', '>=', $request->min_discount);
        }
        if ($request->has('max_discount')) {
            $query->where('discount', '<=', $request->max_discount);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort products
        $sortBy = $request->get('sort', 'discount-high');
        switch ($sortBy) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'discount-high':
                $query->orderBy('discount', 'desc');
                break;
            case 'discount-low':
                $query->orderBy('discount', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('discount', 'desc')->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20);

        // Add computed attributes to products
        $products->getCollection()->transform(function ($product) {
            $product->final_price = $product->final_price;
            $product->main_image = $product->main_image;
            $product->has_stock = $product->has_stock;
            $product->rating = $product->rating;
            $product->reviews_count = $product->reviews_count;
            $product->discount_percentage = round(($product->discount / $product->price) * 100);
            return $product;
        });

        // Get all categories and brands for filters
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $brands = Brand::where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get filtered counts for current filters
        $currentFilters = $request->only(['min_price', 'max_price', 'search']);
        $filteredCategories = $this->getFilteredCategoryCounts($currentFilters);
        $filteredBrands = $this->getFilteredBrandCounts($currentFilters);

        // Calculate sale statistics
        $totalProducts = $products->total();
        $averageDiscount = $products->getCollection()->avg('discount_percentage');
        $maxDiscount = $products->getCollection()->max('discount_percentage');

        return Inertia::render('guest/sales/index', [
            'products' => $products,
            'categories' => $filteredCategories,
            'brands' => $filteredBrands,
            'filters' => $request->only(['brand', 'category', 'min_price', 'max_price', 'min_discount', 'max_discount', 'search', 'sort']),
            'searchQuery' => $request->get('search', ''),
            'stats' => [
                'total_products' => $totalProducts,
                'average_discount' => round($averageDiscount, 1),
                'max_discount' => round($maxDiscount, 1),
            ],
        ]);
    }
}
