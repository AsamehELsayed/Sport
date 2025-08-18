<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandController extends Controller
{
    private function getFilteredCategoryCounts($brandId = null, $filters = [])
    {
        $query = Product::where('is_active', true);

        // Filter by brand if specified
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

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
        $brands = Brand::where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $query = Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true);

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

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort products
        $sortBy = $request->get('sort', 'featured');
        switch ($sortBy) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20);

        // Add computed attributes to products
        $products->getCollection()->transform(function ($product) {
            $product->final_price = $product->final_price;
            $product->main_image = $product->main_image;
            $product->has_stock = $product->has_stock;
            $product->rating = $product->rating;
            $product->reviews_count = $product->reviews_count;
            return $product;
        });

        // Get categories with counts filtered by current filters
        $currentFilters = $request->only(['min_price', 'max_price', 'search']);
        $categories = $this->getFilteredCategoryCounts(null, $currentFilters);

        return Inertia::render('guest/brands/index', [
            'brand' => 'All Brands',
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'filters' => $request->only(['category', 'min_price', 'max_price', 'search', 'sort']),
            'searchQuery' => $request->get('search', ''),
        ]);
    }

    public function show(Request $request, $slug)
    {
        $brand = Brand::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true)
            ->where('brand_id', $brand->id);

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

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort products
        $sortBy = $request->get('sort', 'featured');
        switch ($sortBy) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20);

        // Add computed attributes to products
        $products->getCollection()->transform(function ($product) {
            $product->final_price = $product->final_price;
            $product->main_image = $product->main_image;
            $product->has_stock = $product->has_stock;
            $product->rating = $product->rating;
            $product->reviews_count = $product->reviews_count;
            return $product;
        });

        // Get all brands and categories for filters
        $brands = Brand::where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get categories with counts filtered by current brand and filters
        $currentFilters = $request->only(['min_price', 'max_price', 'search']);
        $categories = $this->getFilteredCategoryCounts($brand->id, $currentFilters);

        return Inertia::render('guest/brands/index', [
            'brand' => $brand->name,
            'brandData' => $brand,
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'filters' => $request->only(['category', 'min_price', 'max_price', 'search', 'sort']),
            'searchQuery' => $request->get('search', ''),
        ]);
    }
}
