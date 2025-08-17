<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'variants'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('admin/products/Index', [
            'products' => $products
        ]);
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('admin/products/Create', [
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|unique:products,sku',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'discount' => 'nullable|numeric|min:0',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'nullable|string',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.sku' => 'nullable|string',
            'variants.*.price_adjustment' => 'nullable|numeric',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
        }

        // Create product
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'images' => $imagePaths,
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'sku' => $validated['sku'],
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'discount' => $validated['discount'] ?? 0,
        ]);

        // Create variants
        foreach ($validated['variants'] as $variantData) {
            $product->variants()->create([
                'size' => $variantData['size'],
                'color' => $variantData['color'],
                'stock' => $variantData['stock'],
                'sku' => $variantData['sku'],
                'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'variants', 'orderItems.order']);

        return Inertia::render('admin/products/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $product->load('variants');

        return Inertia::render('admin/products/Edit', [
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'discount' => 'nullable|numeric|min:0',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'nullable|string',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.sku' => 'nullable|string',
            'variants.*.price_adjustment' => 'nullable|numeric',
        ]);

        // Handle image uploads
        $imagePaths = $product->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
        }

        // Update product
        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'images' => $imagePaths,
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'sku' => $validated['sku'],
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'discount' => $validated['discount'] ?? 0,
        ]);

        // Update variants
        $variantIds = [];
        foreach ($validated['variants'] as $variantData) {
            if (isset($variantData['id'])) {
                // Update existing variant
                $variant = $product->variants()->find($variantData['id']);
                if ($variant) {
                    $variant->update([
                        'size' => $variantData['size'],
                        'color' => $variantData['color'],
                        'stock' => $variantData['stock'],
                        'sku' => $variantData['sku'],
                        'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                    ]);
                    $variantIds[] = $variant->id;
                }
            } else {
                // Create new variant
                $variant = $product->variants()->create([
                    'size' => $variantData['size'],
                    'color' => $variantData['color'],
                    'stock' => $variantData['stock'],
                    'sku' => $variantData['sku'],
                    'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                    'is_active' => true,
                ]);
                $variantIds[] = $variant->id;
            }
        }

        // Remove variants that are no longer in the list
        $product->variants()->whereNotIn('id', $variantIds)->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->count() > 0) {
            return back()->with('error', 'Cannot delete product with associated orders.');
        }

        // Delete product images
        if ($product->images) {
            foreach ($product->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
    }
}
