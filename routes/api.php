<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\OrderController;

Route::prefix('auth')->group(function(){
    Route::post('register', [AuthController::class,'register']);
    Route::post('login', [AuthController::class,'login']);
    Route::post('logout', [AuthController::class,'logout'])->middleware('auth:api');
    Route::get('me', [AuthController::class,'me'])->middleware('auth:api');
});

// Protected
Route::middleware('auth:api')->group(function(){
    // products
    Route::apiResource('products', ProductController::class)->except(['show','create','edit','index']);
    Route::get('products', [ProductController::class,'index']);
    Route::post('products', [ProductController::class,'store']);
    Route::put('products/{product}', [ProductController::class,'update']);
    Route::delete('products/{product}', [ProductController::class,'destroy']);

    // cart
    Route::get('cart', [CartController::class,'index']);
    Route::post('cart', [CartController::class,'add']);
    Route::delete('cart/{id}', [CartController::class,'remove']);

    // orders
    Route::post('orders', [OrderController::class,'store']);
    Route::get('orders', [OrderController::class,'index']);
    Route::get('orders/{order}', [OrderController::class,'show']);
});
