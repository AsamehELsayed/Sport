<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Guest\ProductController;
use App\Http\Controllers\Guest\CategoryController;
use App\Http\Controllers\Guest\BrandController;
use App\Http\Controllers\Guest\SaleController;
use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Simple admin access (for testing)
Route::get('/admin-access', function () {
    return redirect('/admin');
})->name('admin.access');

// Guest product routes
Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('search', [ProductController::class, 'search'])->name('products.search');

Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
Route::get('brands/{brand}', [BrandController::class, 'show'])->name('brands.show');

Route::get('sales', [SaleController::class, 'index'])->name('sales.index');

Route::get('cart', function () {
    return Inertia::render('guest/cart/index');
})->name('cart.index');

Route::get('checkout', function () {
    return Inertia::render('guest/checkout/index');
})->middleware('RequireAuth')->name('checkout.index');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Product details for modal
Route::get('/api/products/{product}/details', function (App\Models\Product $product) {
    if (!$product->is_active) {
        abort(404);
    }

    $product->load(['category', 'brand', 'variants']);

    return response()->json([
        'product' => $product->append(['has_stock', 'total_stock', 'available_sizes', 'available_colors'])
    ]);
})->name('api.products.details');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/customer.php';
require __DIR__.'/admin.php';
