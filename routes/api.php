<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MerchantController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductCategoryController;
use App\Http\Controllers\Api\V1\ProductImageController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\UmkmCategoryController;
use App\Http\Controllers\Api\V1\CartController;

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
    'prefix' => 'v1/auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::group(['middleware' => ['auth.role:system-admin,user']], function () {
        Route::get('/user-account', [AuthController::class, 'userProfile']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// api/v1/customer
Route::group([
    'prefix' => 'v1/customer'
], function ($router) {
    Route::get('/{username}', [CustomerController::class, 'index']);

    Route::group(['middleware' => ['auth.role:user']], function () {
        Route::put('/update', [CustomerController::class, 'update']);
    });
});

// api/v1/merchants
Route::group([
    'prefix' => 'v1/merchants',
], function() {
    Route::get('/', [MerchantController::class, 'index']);
    Route::get('?umkm-category={slug}', [MerchantController::class, 'index']);
    Route::get('/{merchant_id}', [MerchantController::class, 'show']);
    Route::get('/domain/{domain}', [MerchantController::class, 'showByDomain']);
    Route::get('/logo/{filename}', [MerchantController::class, 'getLogo']);

    Route::group(['middleware' => ['auth.role:user']], function () {
        Route::post('/', [MerchantController::class, 'store']);
        Route::put('/update', [MerchantController::class, 'update']);
        Route::delete('/delete', [MerchantController::class, 'destroy']);
    });
});

// api/v1/products
Route::group([
    'prefix' => 'v1/products',
], function() {
    Route::get('', [ProductController::class, 'index']);
    Route::get('?product-category-slug={slug}', [ProductController::class, 'index']);
    Route::get('/{product_id}', [ProductController::class, 'show']);
    Route::get('/image/{filename}', [ProductController::class, 'getImage']);

    Route::group(['middleware' => ['auth.role:user']], function () {
        Route::post('/{merchant_id}', [ProductController::class, 'store']);
        Route::put('/{product_id}', [ProductController::class, 'update']);
        Route::delete('/{product_id}', [ProductController::class, 'destroy']);
        Route::post('/{product_id}/images/add', [ProductImageController::class, 'store']);
        Route::delete('/{product_id}/images/{product_image_id}/delete', [ProductImageController::class, 'destroy']);
    });
});

// api/v1/products-categories
Route::group([
    'prefix' => 'v1/product-categories',
], function() {
    Route::get('/', [ProductCategoryController::class, 'index']);
    Route::get('?umkm-category-slug={slug}', [ProductCategoryController::class, 'index']);

    Route::group(['middleware' => ['auth.role:system-admin']], function () {
        Route::post('/', [ProductCategoryController::class, 'store']);
        Route::delete('/{slug}', [ProductCategoryController::class, 'destroy']);
    });
});

// api/v1/umkm-categories
Route::group([
    'prefix' => 'v1/umkm-categories',
], function() {
    Route::get('/', [UmkmCategoryController::class, 'index']);
    Route::get('/{slug}', [UmkmCategoryController::class, 'getChild']);

    Route::group(['middleware' => ['auth.role:system-admin']], function () {
        Route::post('/', [UmkmCategoryController::class, 'store']);
        Route::delete('/{slug}', [UmkmCategoryController::class, 'destroy']);
    });
});

// api/v1/carts
Route::group([
    'prefix' => 'v1/carts',
    'middleware' => ['auth.role:user']
], function() {
    Route::post('/{product_id}', [CartController::class, 'store']);
    Route::get('/', [CartController::class, 'index']);
});
