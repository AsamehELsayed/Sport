<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    private function getFilteredBrandCounts($categoryId = null, $filters = [])
    {
        $query = Product::where('is_active', true);

        // Filter by category if specified
        if ($categoryId) {
            $query->where('category_id', $categoryId);
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

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true);

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by brand (can be multiple brands)
        if ($request->has('brand')) {
            $brands = explode(',', $request->brand);
            $query->whereHas('brand', function ($q) use ($brands) {
                $q->whereIn('slug', $brands);
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

        // Get categories and brands for filters
        $categories = Category::where('is_active', true)->withCount('products')->orderBy('name')->get();

        // Get brands with counts filtered by current filters
        $currentFilters = $request->only(['min_price', 'max_price', 'search']);
        $brands = $this->getFilteredBrandCounts(null, $currentFilters);

        return Inertia::render('guest/categories/index', [
            'category' => 'All Categories',
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $request->only(['category', 'brand', 'min_price', 'max_price', 'search', 'sort']),
        ]);
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['category', 'brand', 'variants']);

        // Get related products
        $relatedProducts = Product::with(['category', 'brand'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                    ->orWhere('brand_id', $product->brand_id);
            })
            ->limit(4)
            ->get();

        return Inertia::render('guest/products/show', [
            'product' => $product->append('has_stock' , 'total_stock'),
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return redirect()->route('categories.index');
        }

        $products = Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%')
                  ->orWhereHas('category', function ($q) use ($query) {
                      $q->where('name', 'like', '%' . $query . '%');
                  })
                  ->orWhereHas('brand', function ($q) use ($query) {
                      $q->where('name', 'like', '%' . $query . '%');
                  });
            })
            ->paginate(20);

        // Add computed attributes to products
        $products->getCollection()->transform(function ($product) {
            $product->final_price = $product->final_price;
            $product->main_image = $product->main_image;
            $product->has_stock = $product->has_stock;
            $product->rating = $product->rating;
            $product->reviews_count = $product->reviews_count;
            return $product;
        });

        return Inertia::render('guest/categories/index', [
            'category' => 'Search Results',
            'products' => $products,
            'searchQuery' => $query,
            'categories' => Category::where('is_active', true)->withCount('products')->orderBy('name')->get(),
            'brands' => $this->getFilteredBrandCounts(null, ['search' => $query]),
        ]);
    }
}
