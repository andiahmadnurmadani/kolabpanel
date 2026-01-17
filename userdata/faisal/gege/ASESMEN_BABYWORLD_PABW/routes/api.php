<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;

/*
|--------------------------------------------------------------------------
| ADMIN API
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/products', [AdminProductController::class, 'index']);
        Route::post('/products', [AdminProductController::class, 'store']);
        Route::put('/products/{id_produk}', [AdminProductController::class, 'update']);
        Route::delete('/products/{id_produk}', [AdminProductController::class, 'destroy']);
        Route::get('/products/search', [AdminProductController::class, 'search']);
    });

/*
|--------------------------------------------------------------------------
| AUTH API
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PUBLIC PRODUCT API
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

/*
|--------------------------------------------------------------------------
| PROTECTED USER API
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);

    // ACCOUNT
    Route::get('/account', [AccountController::class, 'show']);
    Route::post('/account', [AccountController::class, 'update']);
    Route::delete('/account', [AccountController::class, 'destroy']);

    // ADDRESS
    Route::get('/address', [AddressController::class, 'show']);
    Route::post('/address', [AddressController::class, 'store']);
    Route::put('/address', [AddressController::class, 'update']);

    // PASSWORD
    Route::put('/change-password', [PasswordController::class, 'update']);

    // WISHLIST
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);

    // CART ✅ (INI YANG BENAR)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/update', [CartController::class, 'update']);
    Route::post('/cart/remove', [CartController::class, 'remove']);
    Route::post('/cart/apply-voucher', [CartController::class, 'applyVoucher']);

    // CHECKOUT
    Route::get('/checkout/preview', [CheckoutController::class, 'preview']);
    Route::post('/checkout', [CheckoutController::class, 'store']);
});
