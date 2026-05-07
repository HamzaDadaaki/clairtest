<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/arts', [SiteController::class, 'products'])->name('products.index');
Route::get('/arts/{slug}', [SiteController::class, 'product'])->name('products.show');
Route::post('/arts/{slug}/inquire', [SiteController::class, 'orderInquiry'])->name('products.inquire');
Route::get('/prints', [SiteController::class, 'prints'])->name('prints');
Route::get('/commissions', [SiteController::class, 'commissions'])->name('commissions');
Route::post('/commissions', [SiteController::class, 'commissionSubmit'])->name('commissions.submit');
Route::get('/testimonials', [SiteController::class, 'testimonials'])->name('testimonials');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [SiteController::class, 'contactSubmit'])->name('contact.submit');
Route::post('/subscribe', [SiteController::class, 'subscribe'])->name('subscribe');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{slug}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/add-printful/{id}', [CartController::class, 'addPrintful'])->name('cart.add_printful');
Route::post('/cart/remove/{slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/confirmation/{orderId}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'authenticate'])->name('admin.authenticate');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/products/{slug}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
Route::put('/admin/products/{slug}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
Route::delete('/admin/products/{slug}', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');
Route::post('/admin/testimonials', [AdminController::class, 'storeTestimonial'])->name('admin.testimonials.store');
Route::delete('/admin/testimonials/{id}', [AdminController::class, 'deleteTestimonial'])->name('admin.testimonials.delete');
Route::delete('/admin/subscribers/{id}', [AdminController::class, 'deleteSubscriber'])->name('admin.subscribers.delete');
Route::get('/admin/subscribers/export', [AdminController::class, 'exportSubscribers'])->name('admin.subscribers.export');
Route::put('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
Route::post('/admin/printful/sync', [AdminController::class, 'syncPrintful'])->name('admin.printful.sync');
Route::put('/admin/orders/{id}', [AdminController::class, 'updateOrder'])->name('admin.orders.update');
Route::delete('/admin/orders/{id}', [AdminController::class, 'deleteOrder'])->name('admin.orders.delete');

Route::redirect('/studio-admin', '/admin');
Route::redirect('/studio-admin/login', '/admin/login');
