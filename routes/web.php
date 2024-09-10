<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\DeliveryMethodController;

Route::prefix('/')->group(function () {
    // Home page
    Route::get('/', [GeneralController::class, 'index'])->name('home');
    Route::get('/products', [GeneralController::class, 'products'])->name('products');
    Route::get('/product/{id}', [GeneralController::class, 'show'])->name('product.show');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('admin')->group(function () {
    // dashboard page
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Route to list all payment methods
    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
    // Route to show the form to create a new payment method
    Route::get('/payment-methods/create', [PaymentMethodController::class, 'create'])->name('payment-methods.create');
    // Route to store a newly created payment method
    Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
    // Route to show the form to edit an existing payment method
    Route::get('/payment-methods/{paymentMethod}/edit', [PaymentMethodController::class, 'edit'])->name('payment-methods.edit');
    // Route to update an existing payment method
    Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
    // Route to delete an existing payment method
    Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');


    // Route to list all payment methods
    Route::get('/delivery-methods', [DeliveryMethodController::class, 'index'])->name('delivery-methods.index');
    // Route to show the form to create a new payment method
    Route::get('/delivery-methods/create', [DeliveryMethodController::class, 'create'])->name('delivery-methods.create');
    // Route to store a newly created payment method
    Route::post('/delivery-methods', [DeliveryMethodController::class, 'store'])->name('delivery-methods.store');
    // Route to show the form to edit an existing payment method
    Route::get('/delivery-methods/{paymentMethod}/edit', [DeliveryMethodController::class, 'edit'])->name('payment-methods.edit');
    // Route to update an existing payment method
    Route::put('/delivery-methods/{paymentMethod}', [DeliveryMethodController::class, 'update'])->name('payment-methods.update');
    // Route to delete an existing payment method
    Route::delete('/delivery-methods/{deliveryMethod}', [DeliveryMethodController::class, 'destroy'])->name('delivery-methods.destroy');
})->middleware(AdminMiddleware::class);;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/show', [CartController::class, 'show'])->name('cart.show');
    Route::get('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/proceed', [CheckoutController::class, 'proceedToPayment'])->name('checkout.proceed');
});

require __DIR__ . '/auth.php';
