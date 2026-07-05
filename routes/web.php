<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminShipmentController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DeliveryMethodController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Usercontroller;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;


//PUBLIC ROUTES
Route::prefix('/')->group(function () {
    // Home page
    Route::get('/', [GeneralController::class, 'index'])->name('home');
    Route::get('/products', [GeneralController::class, 'products'])->name('products');
    Route::get('/product/{id}', [GeneralController::class, 'show'])->name('product.show');
    
    // Flash Sale
    Route::get('/flash-sale/{flashSale}', [GeneralController::class, 'flashSaleDetail'])->name('flashsale.show');
    
    // Flash Sale API
    Route::get('/api/flash-sales/status', [GeneralController::class, 'getFlashSalesStatus'])->name('api.flash-sales.status');
});

// WEBHOOK ROUTES (tanpa auth)
Route::post('/webhook/payment/notification', [PaymentController::class, 'notification'])->name('webhook.payment.notification');

// AUTH ROUTES
Route::middleware('auth')->group(function () {
    Route::prefix('/profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/address')->name('address.')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('/create', [AddressController::class, 'create'])->name('create');
        Route::post('/', [AddressController::class, 'store'])->name('store');
        Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
        Route::put('/{address}', [AddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
        Route::patch('/{address}/set-default', [AddressController::class, 'setDefault'])->name('setDefault');
    });

    Route::prefix('/cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::get('/show', [CartController::class, 'show'])->name('show');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::put('/update/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{cartItem}', [CartController::class, 'removeItem'])->name('remove');
    });

    Route::prefix('/checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/buy-now', [CheckoutController::class, 'buyNow'])->name('buy-now');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
        Route::get('/shipping-rates', [CheckoutController::class, 'shippingRates'])->name('shipping-rates');
    });

    Route::prefix('/payment')->name('payment.')->group(function () {
        Route::get('/{order}', [PaymentController::class, 'show'])->name('show');
        Route::post('/notification', [PaymentController::class, 'notification'])->name('notification');
        Route::get('/{order}/success', [PaymentController::class, 'success'])->name('success');
        Route::get('/{order}/finish', [PaymentController::class, 'finish'])->name('finish');
        Route::get('/{order}/unfinish', [PaymentController::class, 'unfinish'])->name('unfinish');
        Route::get('/{order}/error', [PaymentController::class, 'error'])->name('error');
        Route::post('/{order}/retry', [PaymentController::class, 'retry'])->name('retry');
    });

    Route::prefix('/orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/history', [OrderController::class, 'history'])->name('history');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('/transaction')->name('transaction.')->group(function () {

        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
    });
});


Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin,super_admin'])->group(function () {
    // dashboard page
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    Route::prefix('management')->name('management.')->group(function () {

        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        });

        // Discount Management
        Route::prefix('discounts')->name('discounts.')->group(function () {
            Route::get('/', [DiscountController::class, 'index'])->name('index');
            Route::get('/{product}/edit', [DiscountController::class, 'edit'])->name('edit');
            Route::put('/{product}', [DiscountController::class, 'update'])->name('update');
            Route::delete('/{product}', [DiscountController::class, 'destroy'])->name('destroy');
            Route::post('/bulk', [DiscountController::class, 'bulkToggle'])->name('bulk');
        });

        // Flash Sale Management
        Route::prefix('flash-sales')->name('flash_sales.')->group(function () {
            Route::get('/', [FlashSaleController::class, 'index'])->name('index');
            Route::get('/create', [FlashSaleController::class, 'create'])->name('create');
            Route::post('/', [FlashSaleController::class, 'store'])->name('store');
            Route::get('/{flashSale}/edit', [FlashSaleController::class, 'edit'])->name('edit');
            Route::put('/{flashSale}', [FlashSaleController::class, 'update'])->name('update');
            Route::delete('/{flashSale}', [FlashSaleController::class, 'destroy'])->name('destroy');
            Route::post('/{flashSale}/items', [FlashSaleController::class, 'addItem'])->name('addItem');
            Route::delete('/items/{flashSaleItem}', [FlashSaleController::class, 'removeItem'])->name('removeItem');
        });

        // Voucher Management
        Route::prefix('vouchers')->name('vouchers.')->group(function () {
            Route::get('/', [VoucherController::class, 'index'])->name('index');
            Route::get('/create', [VoucherController::class, 'create'])->name('create');
            Route::post('/', [VoucherController::class, 'store'])->name('store');
            Route::get('/{voucher}/edit', [VoucherController::class, 'edit'])->name('edit');
            Route::put('/{voucher}', [VoucherController::class, 'update'])->name('update');
            Route::delete('/{voucher}', [VoucherController::class, 'destroy'])->name('destroy');
            Route::post('/generate-code', [VoucherController::class, 'generateCode'])->name('generateCode');
        });

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/users', [Usercontroller::class, 'index'])->name('index');
            Route::get('/users/{id}/edit', [Usercontroller::class, 'edit'])->name('edit');
            Route::put('/users/{id}', [Usercontroller::class, 'update'])->name('update');
            Route::delete('/users/{id}', [Usercontroller::class, 'destroy'])->name('destroy');
            Route::put('/users/{id}/promote', [Usercontroller::class, 'promote'])->name('promote');
        });

        Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
            Route::get('/create', [PaymentMethodController::class, 'create'])->name('create');
            Route::post('/', [PaymentMethodController::class, 'store'])->name('store');
            Route::get('/{paymentMethod}/edit', [PaymentMethodController::class, 'edit'])->name('edit');
            Route::put('/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('update');
            Route::patch('/{paymentMethod}/toggle-status', [PaymentMethodController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('shipping-methods')->name('shipping-methods.')->group(function () {
            Route::get('/', [ShippingMethodController::class, 'index'])->name('index');
            Route::get('/create', [ShippingMethodController::class, 'create'])->name('create');
            Route::post('/', [ShippingMethodController::class, 'store'])->name('store');
            Route::get('/{shippingMethod}/edit', [ShippingMethodController::class, 'edit'])->name('edit');
            Route::put('/{shippingMethod}', [ShippingMethodController::class, 'update'])->name('update');
            Route::delete('/{shippingMethod}', [ShippingMethodController::class, 'destroy'])->name('destroy');
            Route::patch('/{shippingMethod}/toggle-status', [ShippingMethodController::class, 'toggleStatus'])->name('toggle-status');
        });

        Route::prefix('stores')->name('stores.')->group(function () {
            Route::get('/', [StoreController::class, 'index'])->name('index');
            Route::get('/create', [StoreController::class, 'create'])->name('create');
            Route::post('/', [StoreController::class, 'store'])->name('store');
            Route::get('/{store}/edit', [StoreController::class, 'edit'])->name('edit');
            Route::put('/{store}', [StoreController::class, 'update'])->name('update');
            Route::delete('/{store}', [StoreController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{id}/process', [AdminOrderController::class, 'process'])->name('process');
        Route::patch('/{id}/shipment', [AdminOrderController::class, 'shipment'])->name('shipment');
        Route::patch('/{id}/complete', [AdminOrderController::class, 'complete'])->name('complete');
        Route::patch('/{id}/cancel', [AdminOrderController::class, 'cancel'])->name('cancel');
        Route::patch('/{id}/refund', [AdminOrderController::class, 'refund'])->name('refund');
    });

    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminPaymentController::class, 'show'])->name('show');
    });

    Route::prefix('shipment')->name('shipment.')->group(function () {
        Route::get('/', [AdminShipmentController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminShipmentController::class, 'show'])->name('show');
        Route::patch('/{id}/delivered', [AdminShipmentController::class, 'delivered'])->name('delivered');
        Route::patch('/{id}/cancel', [AdminShipmentController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('transactions')->name('transactions.')->group(function () {

        Route::get('/transactions', [TransactionController::class, 'index'])->name('index');
        Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::get('/transactions/{id}/cancel', [TransactionController::class, 'cancel'])->name('cancel');
        Route::get('/transactions/{id}/accept', [TransactionController::class, 'accept'])->name('accept');
        Route::get('/transactions/{id}/ship', [TransactionController::class, 'ship'])->name('ship');
        Route::get('/transactions/{id}/complete', [TransactionController::class, 'complete'])->name('complete');
        Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('destroy');
    });
});


require __DIR__ . '/auth.php';
