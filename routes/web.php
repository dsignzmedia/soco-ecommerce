<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('frontend.index');
Route::get('/get-started', [HomeController::class, 'getStarted'])->name('frontend.get-started');
Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('frontend.about-us');
Route::get('/services', [HomeController::class, 'services'])->name('frontend.services');
Route::get('/contact', [HomeController::class, 'contact'])->name('frontend.contact');
Route::get('/faq', [HomeController::class, 'faq'])->name('frontend.faq');

// Parent Authentication Routes
Route::get('/parent/login', [AuthController::class, 'parentLogin'])->name('frontend.parent.login');
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
Route::get('/parent/addresses', [AuthController::class, 'myAddresses'])->name('frontend.parent.addresses');

// School Authentication Routes
Route::get('/school/login', [AuthController::class, 'schoolLogin'])->name('frontend.school.login');
Route::post('/school/login', [AuthController::class, 'authenticateSchool'])->name('frontend.school.authenticate');
Route::get('/school/dashboard', [AuthController::class, 'schoolDashboard'])->name('frontend.school.dashboard');
Route::get('/school/reports', [AuthController::class, 'schoolReports'])->name('frontend.school.reports');
Route::post('/school/generate-report', [AuthController::class, 'generateReport'])->name('frontend.school.generate-report');
Route::get('/school/download-report', [AuthController::class, 'downloadReport'])->name('frontend.school.download-report');
Route::post('/school/email-report', [AuthController::class, 'emailReport'])->name('frontend.school.email-report');
