<?php

use App\Project\Modules\System\Cart\ApiControllers\CartController;
use Illuminate\Support\Facades\Route;

Route::controller(CartController::class)->group(function () {
    Route::post('cart/add', 'addToCart')->name('cart.add')->middleware('auth:sanctum');
    Route::put('cart/{cart_item_uuid}', 'updateCartItem')->name('cart.update')->middleware('auth:sanctum');
    Route::delete('cart/{cart_item_uuid}', 'removeFromCart')->name('cart.remove')->middleware('auth:sanctum');
    Route::delete('user/cart/clear', 'clearCart')->name('cart.clear')->middleware('auth:sanctum');
    Route::get('user/cart', 'getUserCart')->name('cart.get')->middleware('auth:sanctum');
});
