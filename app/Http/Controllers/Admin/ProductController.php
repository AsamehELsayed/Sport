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
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|unique:products,sku',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'discount' => 'nullable|numeric|min:0',
            'colorGroups' => 'required|array|min:1',
            'colorGroups.*.color' => 'required|string',
            'colorGroups.*.sizes' => 'required|array|min:1',
            'colorGroups.*.sizes.*.size' => 'required|string',
            'colorGroups.*.sizes.*.stock' => 'required|integer|min:0',
            'colorGroups.*.sizes.*.price_adjustment' => 'nullable|numeric',
            'colorGroups.*.images' => 'nullable|array',
            'colorGroups.*.images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Create product (no main product images)
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'images' => [], // No main product images
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'sku' => $validated['sku'],
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'discount' => $validated['discount'] ?? 0,
        ]);

        // Create variants from color groups
        $hasDefault = false;
        foreach ($validated['colorGroups'] as $colorGroupIndex => $colorGroupData) {
            // Handle color group image uploads
            $colorImagePaths = [];
            if (isset($colorGroupData['images']) && is_array($colorGroupData['images'])) {
                foreach ($colorGroupData['images'] as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $path = $image->store('product-variants', 'public');
                        $colorImagePaths[] = $path;
                    }
                }
            }

            // Create variants for each size in this color
            foreach ($colorGroupData['sizes'] as $sizeIndex => $sizeData) {
                // Set first variant as default if no default is set yet
                $isDefault = !$hasDefault && $colorGroupIndex === 0 && $sizeIndex === 0;
                if ($isDefault) {
                    $hasDefault = true;
                }

                $product->variants()->create([
                    'size' => $sizeData['size'],
                    'color' => $colorGroupData['color'],
                    'images' => $colorImagePaths, // Same images for all sizes of this color
                    'stock' => $sizeData['stock'],
                    'sku' => null, // Optional: generate SKU if needed
                    'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                    'is_active' => true,
                    'is_default' => $isDefault,
                ]);
            }
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
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'discount' => 'nullable|numeric|min:0',
            'colorGroups' => 'required|array|min:1',
            'colorGroups.*.color' => 'required|string',
            'colorGroups.*.sizes' => 'required|array|min:1',
            'colorGroups.*.sizes.*.size' => 'required|string',
            'colorGroups.*.sizes.*.stock' => 'required|integer|min:0',
            'colorGroups.*.sizes.*.price_adjustment' => 'nullable|numeric',
            'colorGroups.*.images' => 'nullable|array',
            'colorGroups.*.images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Update product (no main product images)
        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'images' => [], // No main product images
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'sku' => $validated['sku'],
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'discount' => $validated['discount'] ?? 0,
        ]);

        // Delete all existing variants and recreate from color groups
        $product->variants()->delete();

        // Create variants from color groups
        $hasDefault = false;
        foreach ($validated['colorGroups'] as $colorGroupIndex => $colorGroupData) {
            // Handle color group image uploads
            $colorImagePaths = [];
            if (isset($colorGroupData['images']) && is_array($colorGroupData['images'])) {
                foreach ($colorGroupData['images'] as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $path = $image->store('product-variants', 'public');
                        $colorImagePaths[] = $path;
                    }
                }
            }

            // Create variants for each size in this color
            foreach ($colorGroupData['sizes'] as $sizeIndex => $sizeData) {
                // Set first variant as default if no default is set yet
                $isDefault = !$hasDefault && $colorGroupIndex === 0 && $sizeIndex === 0;
                if ($isDefault) {
                    $hasDefault = true;
                }

                $product->variants()->create([
                    'size' => $sizeData['size'],
                    'color' => $colorGroupData['color'],
                    'images' => $colorImagePaths, // Same images for all sizes of this color
                    'stock' => $sizeData['stock'],
                    'sku' => null, // Optional: generate SKU if needed
                    'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                    'is_active' => true,
                    'is_default' => $isDefault,
                ]);
            }
        }

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
