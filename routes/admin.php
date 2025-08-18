<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::post('orders/bulk-update', [OrderController::class, 'bulkUpdate'])->name('orders.bulk-update');
    Route::get('orders/{order}/generate-invoice', [OrderController::class, 'generateInvoice'])->name('orders.generate-invoice');
    Route::resource('orders', OrderController::class);

    // Products
    Route::resource('products', ProductController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Brands
    Route::resource('brands', BrandController::class);

    // Customers
    Route::resource('customers', CustomerController::class)->except(['create', 'store']);
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');

    // Invoices
    Route::resource('invoices', InvoiceController::class)->except(['create']);
    Route::get('invoices/create/{order_id}', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices/{order_id}', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::post('invoices/bulk-update', [InvoiceController::class, 'bulkUpdate'])->name('invoices.bulk-update');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
    Route::get('invoices/{invoice}/preview', [InvoiceController::class, 'previewPdf'])->name('invoices.preview');
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'sendInvoice'])->name('invoices.send');
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::get('invoices/export/orders', [InvoiceController::class, 'exportOrders'])->name('invoices.export-orders');
    Route::get('invoices/export/customers', [InvoiceController::class, 'exportCustomers'])->name('invoices.export-customers');
});
