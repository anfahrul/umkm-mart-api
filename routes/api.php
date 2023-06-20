<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MerchantController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductCategoryController;
use App\Http\Controllers\Api\V1\ProductImageController;
use App\Http\Controllers\Api\V1\CustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// api/v1/auth
Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/user-account', [AuthController::class, 'userProfile']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// api/v1/customer
Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/customer'
], function ($router) {
    Route::get('/{username}', [CustomerController::class, 'index']);
    Route::put('/{username}', [CustomerController::class, 'update']);
});

// api/v1/merchants
Route::group([
    'middleware' => 'api',
    'prefix' => 'v1',
], function() {
    Route::post('merchants', [MerchantController::class, 'store']);
    Route::get('merchants', [MerchantController::class, 'index']);
    Route::get('merchants?umkm-category={slug}', [MerchantController::class, 'index']);
    Route::get('merchants/{merchant_id}', [MerchantController::class, 'show']);
    Route::get('merchants/domain/{domain}', [MerchantController::class, 'showByDomain']);
    Route::put('merchants/{merchant_id}', [MerchantController::class, 'update']);
    Route::delete('merchants/{merchant_id}', [MerchantController::class, 'destroy']);
});

// api/v1/products
Route::group([
    'middleware' => 'api',
    'prefix' => 'v1',
], function() {
    Route::post('products/{merchant_id}', [ProductController::class, 'store']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products?product-category={slug}', [ProductController::class, 'index']);
    Route::get('products/{product_id}', [ProductController::class, 'show']);
    Route::put('products/{product_id}', [ProductController::class, 'update']);
    Route::delete('products/{product_id}', [ProductController::class, 'destroy']);
    Route::post('products/{product_id}/images/add', [ProductImageController::class, 'store']);
    Route::delete('products/images/{product_image_id}/delete', [ProductImageController::class, 'destroy']);
});

// api/v1/products-categories
Route::group([
    'prefix' => 'v1',
    'namespace' => 'App\Http\Controllers\Api\V1'
], function() {
    Route::post('product_categories', [ProductCategoryController::class, 'store']);
    Route::get('product_categories', [ProductCategoryController::class, 'index']);
});
