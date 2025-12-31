<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Admin\Master\AuthController as MasterAuthController;
use App\Http\Controllers\Admin\Master\CatalogController;
use App\Http\Controllers\Admin\Master\GradeController;
use App\Http\Controllers\Admin\Master\ReportController;
use App\Http\Controllers\Admin\Master\OrderController;
use App\Http\Controllers\Admin\Master\ProductMappingController;
use App\Http\Controllers\Admin\Master\SchoolController;
use App\Http\Controllers\Admin\Master\ShippingController;
use App\Http\Controllers\Admin\Master\SystemSettingsController;
use App\Http\Controllers\Admin\Inventory\AuthController as InventoryAuthController;
use App\Http\Controllers\Admin\Master\InventoryController;
use App\Http\Controllers\Admin\Master\ProductTypeController;
use App\Http\Controllers\Admin\Master\CategoryController;
use App\Http\Controllers\Admin\Master\ProductSettingsController;
use App\Models\User;
use Illuminate\Support\Facades\Log;


Route::get('/', [HomeController::class, 'index'])->name('frontend.index');
Route::get('/get-started', [HomeController::class, 'getStarted'])->name('frontend.get-started');
Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('frontend.about-us');
Route::get('/services', [HomeController::class, 'services'])->name('frontend.services');
Route::get('/contact', [HomeController::class, 'contact'])->name('frontend.contact');
Route::post('/contact', [App\Http\Controllers\Front\ContactController::class, 'store'])->name('frontend.contact.submit');
Route::get('/faq', [HomeController::class, 'faq'])->name('frontend.faq');
Route::get('/return-exchange', [HomeController::class, 'returnExchange'])->name('frontend.return-exchange');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('frontend.privacy-policy');
Route::get('/shipping-policy', [HomeController::class, 'shippingPolicy'])->name('frontend.shipping-policy');
Route::get('/terms-conditions', [HomeController::class, 'termsConditions'])->name('frontend.terms-conditions');

// Public Shop Routes
Route::get('/shop', [App\Http\Controllers\Front\ShopController::class, 'index'])->name('frontend.shop.index');
Route::get('/shop/{id}', [App\Http\Controllers\Front\ShopController::class, 'show'])->name('frontend.shop.detail');
Route::post('/shop/add-to-cart', [App\Http\Controllers\Front\ShopController::class, 'addToCart'])->name('frontend.shop.add-to-cart');

// Unified Login Route (for both parents and schools)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');

Route::get('/clear-all', function () {

    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');

    return ":white_check_mark: All Laravel cache cleared successfully.";
});


Route::get('/fix-storage', function () {



    Artisan::call('storage:link');
Artisan::call('config:clear');
    Artisan::call('cache:clear');
});

// Backward compatibility routes - also point to unified login
Route::get('/parent/login', [AuthController::class, 'showLogin'])->middleware('guest');
Route::get('/school/login', [AuthController::class, 'showLogin'])->middleware('guest');
Route::middleware(['auth', 'prevent-back-history', 'parent'])->group(function () {
    Route::get('/parent/dashboard', [AuthController::class, 'parentDashboard'])->name('frontend.parent.dashboard');
    Route::get('/parent/create-profile', [AuthController::class, 'createProfile'])->name('frontend.parent.create-profile');
    Route::post('/parent/create-profile', [AuthController::class, 'storeProfile'])->name('frontend.parent.store-profile');
    Route::get('/parent/edit-profile/{profileId}', [AuthController::class, 'editProfile'])->name('frontend.parent.edit-profile');
    Route::post('/parent/update-profile/{profileId}', [AuthController::class, 'updateProfile'])->name('frontend.parent.update-profile');
    Route::post('/parent/delete-profile/{profileId}', [AuthController::class, 'deleteProfile'])->name('frontend.parent.delete-profile');
    Route::get('/parent/store', [AuthController::class, 'store'])->name('frontend.parent.store');
    Route::get('/parent/products/{productId}', [AuthController::class, 'productDetail'])->name('frontend.parent.product-detail');
    Route::post('/parent/add-to-cart', [AuthController::class, 'addToCart'])->name('frontend.parent.add-to-cart');
    Route::post('/parent/buy-now', [AuthController::class, 'buyNow'])->name('frontend.parent.buy-now');
    Route::get('/parent/cart', [AuthController::class, 'cart'])->name('frontend.parent.cart');
    Route::post('/parent/remove-from-cart', [AuthController::class, 'removeFromCart'])->name('frontend.parent.remove-from-cart');
    Route::get('/parent/checkout', [AuthController::class, 'checkoutPage'])->name('frontend.parent.checkout');
    Route::post('/parent/checkout', [AuthController::class, 'processCheckout'])->name('frontend.parent.process-checkout');
    Route::post('/parent/save-address', [AuthController::class, 'saveAddress'])->name('frontend.parent.save-address');
    Route::post('/parent/update-address/{addressId}', [AuthController::class, 'updateAddress'])->name('frontend.parent.update-address');
    Route::post('/parent/delete-address/{addressId}', [AuthController::class, 'deleteAddress'])->name('frontend.parent.delete-address');
    Route::get('/parent/orders', [AuthController::class, 'orders'])->name('frontend.parent.orders');
    Route::get('/parent/track-order/{orderId}', [AuthController::class, 'trackOrder'])->name('frontend.parent.track-order');
    Route::get('/parent/return-exchange/{orderId}', [AuthController::class, 'returnExchange'])->name('frontend.parent.return-exchange');
    Route::post('/parent/request-return-exchange', [AuthController::class, 'requestReturnExchange'])->name('frontend.parent.request-return-exchange');
    Route::get('/parent/account', [AuthController::class, 'accountDetails'])->name('frontend.parent.account');
    Route::get('/parent/profile', [AuthController::class, 'profile'])->name('frontend.parent.profile');
    Route::post('/parent/profile', [AuthController::class, 'updateProfileDetails'])->name('frontend.parent.update-profile-details');
    Route::get('/parent/addresses', [AuthController::class, 'myAddresses'])->name('frontend.parent.addresses');
    Route::post('/parent/save-address', [AuthController::class, 'saveAddress'])->name('frontend.parent.save-address');
    Route::post('/parent/delete-address/{addressId}', [AuthController::class, 'deleteAddress'])->name('frontend.parent.delete-address');

    // Product Reviews
    // Payment Routes
    Route::post('/parent/payment/initiate', [AuthController::class, 'initiateRazorpay'])->name('frontend.parent.payment.initiate');
    Route::post('/parent/payment/verify', [AuthController::class, 'verifyRazorpay'])->name('frontend.parent.payment.verify');
    Route::post('/parent/process-checkout-redirect', [AuthController::class, 'processCheckout'])->name('frontend.parent.process-checkout-redirect');
});

// Google OAuth Routes
Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('login.google.callback');

// Registration Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Login Route
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp'])->name('auth.send-otp');
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.verify-otp');
Route::post('/auth/set-guest-mode', [AuthController::class, 'setGuestMode'])->name('auth.set-guest-mode');
Route::post('/auth/mark-welcome-seen', [AuthController::class, 'markWelcomeModalSeen'])->name('auth.mark-welcome-seen');

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// School Authentication Routes (POST only, GET uses unified /login route)
Route::post('/school/login', [AuthController::class, 'authenticateSchool'])->name('frontend.school.authenticate');
Route::post('/school/logout', [AuthController::class, 'logout'])->name('frontend.school.logout');

Route::middleware(['auth', 'school'])->group(function () {
    Route::get('/school/dashboard', [AuthController::class, 'schoolDashboard'])->name('frontend.school.dashboard');
    Route::get('/school/orders', [AuthController::class, 'schoolOrders'])->name('frontend.school.orders');
    Route::get('/school/students', [AuthController::class, 'schoolStudents'])->name('frontend.school.students');
    Route::get('/school/products', [AuthController::class, 'schoolProducts'])->name('frontend.school.products');
    Route::get('/school/settings', [AuthController::class, 'schoolSettings'])->name('frontend.school.settings');
    Route::post('/school/settings', [AuthController::class, 'updateSchoolSettings'])->name('frontend.school.settings.update');
    Route::get('/school/reports', [AuthController::class, 'schoolReports'])->name('frontend.school.reports');
    Route::post('/school/generate-report', [AuthController::class, 'generateReport'])->name('frontend.school.generate-report');
    Route::get('/school/download-report', [AuthController::class, 'downloadReport'])->name('frontend.school.download-report');
    Route::post('/school/email-report', [AuthController::class, 'emailReport'])->name('frontend.school.email-report');
});

// Master Admin Routes
Route::prefix('MasterAdmin')->name('master.admin.')->group(function () {
    // Guest-only routes (redirect to dashboard if already logged in)
    Route::middleware('master.admin.guest')->group(function () {
        Route::get('/login', [MasterAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [MasterAuthController::class, 'login'])->name('login.submit');
    });

    // Protected routes (require master.admin middleware)
    Route::middleware('master.admin')->group(function () {
        Route::get('/dashboard', [MasterAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [MasterAuthController::class, 'profile'])->name('profile');
        Route::post('/profile/password', [MasterAuthController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('/logout', [MasterAuthController::class, 'logout'])->name('logout');

        Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
        Route::get('/catalog/create', [CatalogController::class, 'create'])->name('catalog.create');
        Route::post('/catalog', [CatalogController::class, 'store'])->name('catalog.store');
        Route::get('/catalog/export/{type}', [CatalogController::class, 'export'])->name('catalog.export');
        Route::get('/catalog/{productMapping}/edit', [CatalogController::class, 'edit'])->name('catalog.edit');
        Route::put('/catalog/{productMapping}', [CatalogController::class, 'update'])->name('catalog.update');
        Route::get('/catalog/{productMapping}', [CatalogController::class, 'show'])->name('catalog.show');
        Route::delete('/catalog/{productMapping}', [CatalogController::class, 'destroy'])->name('catalog.destroy');

        Route::get('/shipping', [ShippingController::class, 'edit'])->name('shipping.edit');
        Route::post('/shipping', [ShippingController::class, 'update'])->name('shipping.update');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');


        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}/invoice/download', [OrderController::class, 'invoiceDownload'])->name('orders.invoice.download');
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoiceView'])->name('orders.invoice');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        // Payments
        Route::get('/payments', [App\Http\Controllers\Admin\Master\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [App\Http\Controllers\Admin\Master\PaymentController::class, 'show'])->name('payments.show');

        Route::resource('schools', SchoolController::class)->except('show');
        Route::get('schools/{school}/grades', [GradeController::class, 'index'])->name('schools.grades.index');
        Route::post('schools/{school}/grades', [GradeController::class, 'store'])->name('schools.grades.store');
        Route::put('schools/{school}/grades/{grade}', [GradeController::class, 'update'])->name('schools.grades.update');
        Route::delete('schools/{school}/grades/{grade}', [GradeController::class, 'destroy'])->name('schools.grades.destroy');

        Route::get('schools/{school}/product-mapping', [ProductMappingController::class, 'index'])->name('schools.product-mapping.index');
        Route::post('schools/{school}/product-mapping', [ProductMappingController::class, 'store'])->name('schools.product-mapping.store');
        Route::put('schools/{school}/product-mapping/{productMapping}', [ProductMappingController::class, 'update'])->name('schools.product-mapping.update');
        Route::delete('schools/{school}/product-mapping/{productMapping}', [ProductMappingController::class, 'destroy'])->name('schools.product-mapping.destroy');

        // System Settings
        Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index');

        // Payment Gateways
        Route::get('/settings/payment-gateways', [SystemSettingsController::class, 'paymentGateways'])->name('settings.payment-gateways');
        Route::post('/settings/payment-gateways', [SystemSettingsController::class, 'storePaymentGateway'])->name('settings.payment-gateways.store');
        Route::put('/settings/payment-gateways/{paymentGateway}', [SystemSettingsController::class, 'updatePaymentGateway'])->name('settings.payment-gateways.update');
        Route::delete('/settings/payment-gateways/{paymentGateway}', [SystemSettingsController::class, 'destroyPaymentGateway'])->name('settings.payment-gateways.destroy');

        // Invoice Templates
        Route::get('/settings/invoice-templates', [SystemSettingsController::class, 'invoiceTemplates'])->name('settings.invoice-templates');
        Route::post('/settings/invoice-templates', [SystemSettingsController::class, 'storeInvoiceTemplate'])->name('settings.invoice-templates.store');
        Route::put('/settings/invoice-templates/{invoiceTemplate}', [SystemSettingsController::class, 'updateInvoiceTemplate'])->name('settings.invoice-templates.update');
        Route::delete('/settings/invoice-templates/{invoiceTemplate}', [SystemSettingsController::class, 'destroyInvoiceTemplate'])->name('settings.invoice-templates.destroy');

        // Email Templates
        Route::get('/settings/email-templates', [SystemSettingsController::class, 'emailTemplates'])->name('settings.email-templates');
        Route::post('/settings/email-templates', [SystemSettingsController::class, 'storeEmailTemplate'])->name('settings.email-templates.store');
        Route::put('/settings/email-templates/{emailTemplate}', [SystemSettingsController::class, 'updateEmailTemplate'])->name('settings.email-templates.update');
        Route::delete('/settings/email-templates/{emailTemplate}', [SystemSettingsController::class, 'destroyEmailTemplate'])->name('settings.email-templates.destroy');

        // SMS Templates
        Route::get('/settings/sms-templates', [SystemSettingsController::class, 'smsTemplates'])->name('settings.sms-templates');
        Route::post('/settings/sms-templates', [SystemSettingsController::class, 'storeSmsTemplate'])->name('settings.sms-templates.store');
        Route::put('/settings/sms-templates/{smsTemplate}', [SystemSettingsController::class, 'updateSmsTemplate'])->name('settings.sms-templates.update');
        Route::delete('/settings/sms-templates/{smsTemplate}', [SystemSettingsController::class, 'destroySmsTemplate'])->name('settings.sms-templates.destroy');

        // App Branding
        Route::get('/settings/app-branding', [SystemSettingsController::class, 'appBranding'])->name('settings.app-branding');
        Route::put('/settings/app-branding', [SystemSettingsController::class, 'updateAppBranding'])->name('settings.app-branding.update');
        Route::get('/settings/audit-logs', [SystemSettingsController::class, 'auditLogs'])->name('settings.audit-logs');

        // Backups
        Route::get('/settings/backups', [SystemSettingsController::class, 'backups'])->name('settings.backups');
        Route::get('/settings/backups/create', fn () => redirect()->route('master.admin.settings.backups'))->name('settings.backups.create.form');
        Route::post('/settings/backups', [SystemSettingsController::class, 'createBackup'])->name('settings.backups.create');
        Route::get('/settings/backups/{backup}/download', [SystemSettingsController::class, 'downloadBackup'])->name('settings.backups.download');
        Route::post('/settings/backups/{backup}/restore', [SystemSettingsController::class, 'restoreBackup'])->name('settings.backups.restore');
        Route::delete('/settings/backups/{backup}', [SystemSettingsController::class, 'destroyBackup'])->name('settings.backups.destroy');

        // Inventory
        Route::get('/inventory', [InventoryController::class, 'dashboard'])->name('inventory.dashboard');
        Route::get('/inventory/list', [InventoryController::class, 'index'])->name('inventory.list');
        Route::get('/inventory/reports', [InventoryController::class, 'reports'])->name('inventory.reports');
        Route::get('/inventory/{product}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('/inventory/{product}/adjust', [InventoryController::class, 'applyAdjustment'])->name('inventory.adjust.apply');
        Route::post('/inventory/{product}/variant-stock', [InventoryController::class, 'updateVariantStock'])->name('inventory.variant-stock.update');

        // Product Settings (Combined Product Types & Categories)
        Route::get('/product-settings', [ProductSettingsController::class, 'index'])->name('product-settings.index');

        // Product Types Management (keep routes for CRUD operations)
        Route::resource('product-types', ProductTypeController::class)->names([
            'index' => 'product-types.index',
            'create' => 'product-types.create',
            'store' => 'product-types.store',
            'edit' => 'product-types.edit',
            'update' => 'product-types.update',
            'destroy' => 'product-types.destroy',
        ])->except(['index']);

        // Categories Management (keep routes for CRUD operations)
        Route::resource('categories', CategoryController::class)->names([
            'index' => 'categories.index',
            'create' => 'categories.create',
            'store' => 'categories.store',
            'edit' => 'categories.edit',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy',
        ])->except(['index']);

        // Returns & Exchanges
        Route::get('/returns-exchange', [\App\Http\Controllers\Admin\Master\ReturnExchangeController::class, 'index'])->name('returns-exchange.index');
        Route::get('/returns-exchange/{returnRequest}', [\App\Http\Controllers\Admin\Master\ReturnExchangeController::class, 'show'])->name('returns-exchange.show');
        Route::post('/returns-exchange/{returnRequest}/approve', [\App\Http\Controllers\Admin\Master\ReturnExchangeController::class, 'approve'])->name('returns-exchange.approve');
        Route::post('/returns-exchange/{returnRequest}/receive', [\App\Http\Controllers\Admin\Master\ReturnExchangeController::class, 'receive'])->name('returns-exchange.receive');
        Route::post('/returns-exchange/{returnRequest}/generate-exchange', [\App\Http\Controllers\Admin\Master\ReturnExchangeController::class, 'generateExchange'])->name('returns-exchange.generate');
        Route::post('/returns-exchange/{returnRequest}/switch-type', [\App\Http\Controllers\Admin\Master\ReturnExchangeController::class, 'switchType'])->name('returns-exchange.switch-type');
        Route::post('/returns-exchange/{returnRequest}/deny', [\App\Http\Controllers\Admin\Master\ReturnExchangeController::class, 'deny'])->name('returns-exchange.deny');
        Route::post('/returns-exchange/{returnRequest}/refund', [\App\Http\Controllers\Admin\Master\ReturnExchangeController::class, 'refund'])->name('returns-exchange.refund');
        // Notifications
        Route::match(['get', 'post'], '/notifications/{notification}/read', [MasterAuthController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [MasterAuthController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    });
});

// Inventory Admin Routes
Route::prefix('InventoryAdmin')->name('inventory.admin.')->group(function () {
    // Guest-only routes (redirect to dashboard if already logged in)
    Route::middleware('inventory.admin.guest')->group(function () {
        Route::get('/login', [InventoryAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [InventoryAuthController::class, 'login'])->name('login.submit');
    });

    // Protected routes (require inventory.admin middleware)
    Route::middleware('inventory.admin')->group(function () {
        Route::get('/dashboard', [InventoryAuthController::class, 'dashboard'])->name('dashboard');
        // Profile Settings
        Route::get('/profile', [InventoryAuthController::class, 'profile'])->name('profile');
        Route::post('/profile/password', [InventoryAuthController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('/logout', [InventoryAuthController::class, 'logout'])->name('logout');

        // Orders
        Route::get('/orders', [App\Http\Controllers\Admin\Inventory\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/shipping', [App\Http\Controllers\Admin\Inventory\OrderController::class, 'shipping'])->name('orders.shipping');
        Route::get('/orders/{order}', [App\Http\Controllers\Admin\Inventory\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/status', [App\Http\Controllers\Admin\Inventory\OrderController::class, 'updateStatus'])->name('orders.status');
        Route::get('/orders/{order}/packing-slip', [App\Http\Controllers\Admin\Inventory\OrderController::class, 'packingSlip'])->name('orders.packing-slip');
        Route::get('/orders/{order}/print-label', [App\Http\Controllers\Admin\Inventory\OrderController::class, 'printLabel'])->name('orders.print-label');

        // Inventory
        Route::get('/inventory', [App\Http\Controllers\Admin\Inventory\InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/{product}/adjust', [App\Http\Controllers\Admin\Inventory\InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('/inventory/{product}/adjust', [App\Http\Controllers\Admin\Inventory\InventoryController::class, 'applyAdjustment'])->name('inventory.adjust.apply');
        Route::post('/inventory/{product}/variant-stock', [App\Http\Controllers\Admin\Inventory\InventoryController::class, 'updateVariantStock'])->name('inventory.variant-stock.update');
        Route::get('/reports', [App\Http\Controllers\Admin\Inventory\InventoryController::class, 'reports'])->name('reports.index');

        // Notifications
        Route::match(['get', 'post'], '/notifications/{notification}/read', [InventoryAuthController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [InventoryAuthController::class, 'markAllNotificationsRead'])->name('notifications.read-all');


        // Returns & Exchanges
        Route::get('/returns-exchange', [App\Http\Controllers\Admin\Inventory\ReturnExchangeController::class, 'index'])->name('returns-exchange.index');
        Route::get('/returns-exchange/{returnRequest}', [App\Http\Controllers\Admin\Inventory\ReturnExchangeController::class, 'show'])->name('returns-exchange.show');
        Route::post('/returns-exchange/{returnRequest}/approve', [App\Http\Controllers\Admin\Inventory\ReturnExchangeController::class, 'approve'])->name('returns-exchange.approve');
        Route::post('/returns-exchange/{returnRequest}/receive', [App\Http\Controllers\Admin\Inventory\ReturnExchangeController::class, 'receive'])->name('returns-exchange.receive');
        Route::post('/returns-exchange/{returnRequest}/generate-exchange', [App\Http\Controllers\Admin\Inventory\ReturnExchangeController::class, 'generateExchange'])->name('returns-exchange.generate');
        Route::post('/returns-exchange/{returnRequest}/switch-type', [App\Http\Controllers\Admin\Inventory\ReturnExchangeController::class, 'switchType'])->name('returns-exchange.switch-type');
        Route::post('/returns-exchange/{returnRequest}/deny', [App\Http\Controllers\Admin\Inventory\ReturnExchangeController::class, 'deny'])->name('returns-exchange.deny');
        Route::post('/returns-exchange/{returnRequest}/refund', [App\Http\Controllers\Admin\Inventory\ReturnExchangeController::class, 'refund'])->name('returns-exchange.refund');
    });
});

// Store Admin Portal Login (Unified for BTS/Merch)
Route::prefix('StoreAdmin')->name('store.admin.')->group(function () {
    // Middleware removed to allow simultaneous login (BTS + Merch) from the same portal page
    Route::get('/login', [App\Http\Controllers\Admin\PortalAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\PortalAuthController::class, 'login'])->name('login.submit');
    
    Route::post('/logout', [App\Http\Controllers\Admin\PortalAuthController::class, 'logout'])->name('logout');
});

// Back-To-School Admin Routes
Route::prefix('BackToSchoolAdmin')->name('admin.back_to_school.')->group(function () {
    Route::middleware(['auth:bts_admin', 'admin.back_to_school'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\BackToSchool\DashboardController::class, 'index'])->name('dashboard');
        
        // Reports
        Route::get('/reports', [App\Http\Controllers\Admin\BackToSchool\ReportController::class, 'index'])->name('reports.index');
        
        // Inventory
        Route::get('/inventory', [App\Http\Controllers\Admin\BackToSchool\InventoryController::class, 'index'])->name('inventory.index');
        Route::put('/inventory/{product}', [App\Http\Controllers\Admin\BackToSchool\InventoryController::class, 'update'])->name('inventory.update');
        Route::post('/inventory/{product}/variant-stock', [App\Http\Controllers\Admin\BackToSchool\InventoryController::class, 'updateVariantStock'])->name('inventory.variant-stock.update');

        Route::get('/products/export', [App\Http\Controllers\Admin\BackToSchool\ProductController::class, 'export'])->name('products.export');
        Route::resource('products', App\Http\Controllers\Admin\BackToSchool\ProductController::class);
        Route::get('/orders/{order}/invoice/download', [App\Http\Controllers\Admin\BackToSchool\OrderController::class, 'invoiceDownload'])->name('orders.invoice.download');
        Route::get('/orders/{order}/invoice', [App\Http\Controllers\Admin\BackToSchool\OrderController::class, 'invoiceView'])->name('orders.invoice');
        Route::post('/orders/{order}/status', [App\Http\Controllers\Admin\BackToSchool\OrderController::class, 'updateStatus'])->name('orders.status');
        Route::resource('orders', App\Http\Controllers\Admin\BackToSchool\OrderController::class);
        
        // Product Settings (alias for BTS admins)
        Route::get('/product-settings', [App\Http\Controllers\Admin\Master\ProductSettingsController::class, 'index'])->name('product-settings.index');
        
        // Categories (using same controller, but with BTS context)
        Route::resource('categories', App\Http\Controllers\Admin\Master\CategoryController::class)->names([
            'create' => 'categories.create',
            'store' => 'categories.store',
            'edit' => 'categories.edit',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy',
        ]);

        // Returns & Exchanges
        Route::get('/returns-exchange', [App\Http\Controllers\Admin\BackToSchool\ReturnExchangeController::class, 'index'])->name('returns-exchange.index');
        Route::get('/returns-exchange/{returnRequest}', [App\Http\Controllers\Admin\BackToSchool\ReturnExchangeController::class, 'show'])->name('returns-exchange.show');
        Route::post('/returns-exchange/{returnRequest}/approve', [App\Http\Controllers\Admin\BackToSchool\ReturnExchangeController::class, 'approve'])->name('returns-exchange.approve');
        Route::post('/returns-exchange/{returnRequest}/receive', [App\Http\Controllers\Admin\BackToSchool\ReturnExchangeController::class, 'receive'])->name('returns-exchange.receive');
        Route::post('/returns-exchange/{returnRequest}/generate-exchange', [App\Http\Controllers\Admin\BackToSchool\ReturnExchangeController::class, 'generateExchange'])->name('returns-exchange.generate');
        Route::post('/returns-exchange/{returnRequest}/switch-type', [App\Http\Controllers\Admin\BackToSchool\ReturnExchangeController::class, 'switchType'])->name('returns-exchange.switch-type');
        Route::post('/returns-exchange/{returnRequest}/deny', [App\Http\Controllers\Admin\BackToSchool\ReturnExchangeController::class, 'deny'])->name('returns-exchange.deny');
        Route::post('/returns-exchange/{returnRequest}/refund', [App\Http\Controllers\Admin\BackToSchool\ReturnExchangeController::class, 'refund'])->name('returns-exchange.refund');

        // Payments
        Route::get('/payments', [App\Http\Controllers\Admin\BackToSchool\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [App\Http\Controllers\Admin\BackToSchool\PaymentController::class, 'show'])->name('payments.show');
    });
});

// Merchandise Admin Routes
Route::prefix('MerchandiseAdmin')->name('admin.merchandise.')->group(function () {
    Route::middleware(['auth:merch_admin', 'admin.merchandise'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\Merchandise\DashboardController::class, 'index'])->name('dashboard');

        // Reports
        Route::get('/reports', [App\Http\Controllers\Admin\Merchandise\ReportController::class, 'index'])->name('reports.index');
        
        // Inventory
        Route::get('/inventory', [App\Http\Controllers\Admin\Merchandise\InventoryController::class, 'index'])->name('inventory.index');
        Route::put('/inventory/{product}', [App\Http\Controllers\Admin\Merchandise\InventoryController::class, 'update'])->name('inventory.update');
        Route::post('/inventory/{product}/variant-stock', [App\Http\Controllers\Admin\Merchandise\InventoryController::class, 'updateVariantStock'])->name('inventory.variant-stock.update');
        Route::get('/products/export', [App\Http\Controllers\Admin\Merchandise\ProductController::class, 'export'])->name('products.export');
        Route::resource('products', App\Http\Controllers\Admin\Merchandise\ProductController::class);
        Route::get('/orders/{order}/invoice/download', [App\Http\Controllers\Admin\Merchandise\OrderController::class, 'invoiceDownload'])->name('orders.invoice.download');
        Route::get('/orders/{order}/invoice', [App\Http\Controllers\Admin\Merchandise\OrderController::class, 'invoiceView'])->name('orders.invoice');
        Route::post('/orders/{order}/status', [App\Http\Controllers\Admin\Merchandise\OrderController::class, 'updateStatus'])->name('orders.status');
        Route::resource('orders', App\Http\Controllers\Admin\Merchandise\OrderController::class);
        Route::resource('print-queue', App\Http\Controllers\Admin\Merchandise\PrintQueueController::class);
        
        // Product Settings (alias for merch admins)
        Route::get('/product-settings', [App\Http\Controllers\Admin\Master\ProductSettingsController::class, 'index'])->name('product-settings.index');
        
        // Categories (using same controller, but with Merch context)
        Route::resource('categories', App\Http\Controllers\Admin\Master\CategoryController::class)->names([
            'create' => 'categories.create',
            'store' => 'categories.store',
            'edit' => 'categories.edit',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy',
        ]);

        // Returns & Exchanges
        Route::get('/returns-exchange', [App\Http\Controllers\Admin\Merchandise\ReturnExchangeController::class, 'index'])->name('returns-exchange.index');
        Route::get('/returns-exchange/{returnRequest}', [App\Http\Controllers\Admin\Merchandise\ReturnExchangeController::class, 'show'])->name('returns-exchange.show');
        Route::post('/returns-exchange/{returnRequest}/approve', [App\Http\Controllers\Admin\Merchandise\ReturnExchangeController::class, 'approve'])->name('returns-exchange.approve');
        Route::post('/returns-exchange/{returnRequest}/receive', [App\Http\Controllers\Admin\Merchandise\ReturnExchangeController::class, 'receive'])->name('returns-exchange.receive');
        Route::post('/returns-exchange/{returnRequest}/generate-exchange', [App\Http\Controllers\Admin\Merchandise\ReturnExchangeController::class, 'generateExchange'])->name('returns-exchange.generate');
        Route::post('/returns-exchange/{returnRequest}/switch-type', [App\Http\Controllers\Admin\Merchandise\ReturnExchangeController::class, 'switchType'])->name('returns-exchange.switch-type');
        Route::post('/returns-exchange/{returnRequest}/deny', [App\Http\Controllers\Admin\Merchandise\ReturnExchangeController::class, 'deny'])->name('returns-exchange.deny');
        Route::post('/returns-exchange/{returnRequest}/refund', [App\Http\Controllers\Admin\Merchandise\ReturnExchangeController::class, 'refund'])->name('returns-exchange.refund');

        // Payments
        Route::get('/payments', [App\Http\Controllers\Admin\Merchandise\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [App\Http\Controllers\Admin\Merchandise\PaymentController::class, 'show'])->name('payments.show');
    });
});

