<?php

use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('customer.login');
    Route::post('login', [AuthController::class, 'login']);

    Route::get('register', [AuthController::class, 'showRegister'])->name('customer.register');
    Route::post('register', [AuthController::class, 'register']);

    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('customer.password.request');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('customer.password.reset');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('customer.logout');

    // Profile routes
    Route::get('profile', [ProfileController::class, 'show'])->name('customer.profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('customer.profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('customer.profile.password');
    Route::put('profile/photo', [ProfileController::class, 'updatePhoto'])->name('customer.profile.photo');
    Route::delete('profile/photo', [ProfileController::class, 'deletePhoto'])->name('customer.profile.photo.delete');
    Route::delete('profile', [ProfileController::class, 'deleteAccount'])->name('customer.profile.delete');

    // Order routes
    Route::get('orders', [OrderController::class, 'index'])->name('customer.orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('customer.orders.show');
    Route::post('orders', [OrderController::class, 'store'])->name('customer.orders.store');

    // Wishlist routes
    Route::middleware('wishlist')->group(function () {
        Route::get('wishlist', [WishlistController::class, 'index'])->name('customer.wishlist.index');
        Route::post('wishlist', [WishlistController::class, 'store'])->name('customer.wishlist.store');
        Route::delete('wishlist', [WishlistController::class, 'destroy'])->name('customer.wishlist.destroy');
        Route::post('wishlist/toggle', [WishlistController::class, 'toggle'])->name('customer.wishlist.toggle');
        Route::post('wishlist/check', [WishlistController::class, 'check'])->name('customer.wishlist.check');
        Route::get('wishlist/count', [WishlistController::class, 'count'])->name('customer.wishlist.count');
    });
});
