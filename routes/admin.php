<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerGroupController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\EmailCampaignController;
use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\Settings\AccountController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['RequireAuth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
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

    // Customer Groups
    Route::resource('customer-groups', CustomerGroupController::class);
    Route::post('customer-groups/{customerGroup}/add-customers', [CustomerGroupController::class, 'addCustomers'])->name('customer-groups.add-customers');
    Route::post('customer-groups/{customerGroup}/remove-customers', [CustomerGroupController::class, 'removeCustomers'])->name('customer-groups.remove-customers');
    Route::get('customer-groups/{customerGroup}/available-customers', [CustomerGroupController::class, 'getAvailableCustomers'])->name('customer-groups.available-customers');

    // Invoices
    Route::resource('invoices', InvoiceController::class)->except(['create', 'store']);
    Route::get('invoices/create/{order_id}', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices/{order_id}', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::post('invoices/bulk-update', [InvoiceController::class, 'bulkUpdate'])->name('invoices.bulk-update');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
    Route::get('invoices/{invoice}/preview', [InvoiceController::class, 'previewPdf'])->name('invoices.preview');
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'sendInvoice'])->name('invoices.send');
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::get('invoices/export/orders', [InvoiceController::class, 'exportOrders'])->name('invoices.export-orders');
    Route::get('invoices/export/customers', [InvoiceController::class, 'exportCustomers'])->name('invoices.export-customers');

    // Email Templates
    Route::resource('email-templates', EmailTemplateController::class);
    Route::get('email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');

    // Email Campaigns
    Route::resource('email-campaigns', EmailCampaignController::class);
    Route::post('email-campaigns/{emailCampaign}/send', [EmailCampaignController::class, 'send'])->name('email-campaigns.send');
    Route::get('email-campaigns/{emailCampaign}/preview', [EmailCampaignController::class, 'preview'])->name('email-campaigns.preview');
    Route::get('email-campaigns/{emailCampaign}/recipients', [EmailCampaignController::class, 'getRecipients'])->name('email-campaigns.recipients');

    // Email Settings
    Route::get('email-settings', [EmailSettingController::class, 'index'])->name('email-settings.index');
    Route::post('email-settings', [EmailSettingController::class, 'store'])->name('email-settings.store');
    Route::post('email-settings/test', [EmailSettingController::class, 'test'])->name('email-settings.test');

    // Settings
    Route::redirect('settings', '/admin/settings/profile');
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/account', [AccountController::class, 'edit'])->name('settings.account.edit');
    Route::patch('settings/account', [AccountController::class, 'update'])->name('settings.account.update');
    Route::get('settings/password', [PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('settings.password.update');
});
