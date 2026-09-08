<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopTheLookController;
use App\Http\Controllers\Admin\LookController;
use App\Http\Controllers\Admin\LookVariantController;

Route::middleware('storefront')->group(function () {
    Route::get('/', [StorefrontController::class, 'index'])
        ->name('store.index');

    Route::get('/looks', [ShopTheLookController::class, 'index'])
        ->name('looks.index');

    Route::get('/looks/{look}', [ShopTheLookController::class, 'show'])
        ->name('looks.show');

    Route::get('/products/{product}', [StorefrontController::class, 'show'])
        ->name('store.show');
});

// Admin routes
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('categories', CategoryController::class);

        Route::resource('looks', LookController::class)->except(['show']);
        Route::get('looks/{look}/variants/create', [LookVariantController::class, 'create'])->name('looks.variants.create');
        Route::post('looks/{look}/variants', [LookVariantController::class, 'store'])->name('looks.variants.store');
        Route::get('looks/{look}/variants/{variant}/edit', [LookVariantController::class, 'edit'])->name('looks.variants.edit');
        Route::put('looks/{look}/variants/{variant}', [LookVariantController::class, 'update'])->name('looks.variants.update');
        Route::delete('looks/{look}/variants/{variant}', [LookVariantController::class, 'destroy'])->name('looks.variants.destroy');

        Route::resource('products', ProductController::class);

        Route::post('products/{product}/images', [ProductController::class, 'storeImage'])
            ->name('products.images.store');

        Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])
            ->name('products.images.destroy');

        Route::patch('products/{product}/images/{image}/primary', [ProductController::class, 'setPrimaryImage'])
            ->name('products.images.primary');

        // Admin order management routes
        Route::get('orders', [OrderController::class, 'index'])
            ->name('orders.index');

        Route::get('orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.status');
    });

// Cart routes
Route::middleware('storefront')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/cart/checkout', [CartController::class, 'checkout'])
        ->name('cart.checkout');

    Route::post('/cart/{product}', [CartController::class, 'add'])
        ->name('cart.add');

    Route::patch('/cart/{product}', [CartController::class, 'update'])
        ->name('cart.update');

    Route::delete('/cart/{product}', [CartController::class, 'remove'])
        ->name('cart.remove');

    Route::delete('/cart', [CartController::class, 'clear'])
        ->name('cart.clear');

});

// Checkout routes
Route::middleware(['auth', 'storefront'])->group(function () {
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');

    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');
});

// Orders routes
Route::middleware(['auth', 'storefront'])->group(function () {
    Route::get('/orders', [OrdersController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/{order}', [OrdersController::class, 'show'])
        ->name('orders.show');

    Route::patch('/orders/{order}/cancel', [OrdersController::class, 'cancel'])
        ->name('orders.cancel');
});
// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__ . '/auth.php';
