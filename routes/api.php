<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MerchantController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductCategoryController;

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
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// api/v1/merchants
Route::group([
    'middleware' => 'api',
    'prefix' => 'v1',
    // 'namespace' => 'App\Http\Controllers\Api\V1'
], function() {
    Route::post('merchants', [MerchantController::class, 'store']);
    Route::get('merchants', [MerchantController::class, 'index']);
    Route::get('merchants/{merchant_id}', [MerchantController::class, 'show']);
    Route::put('merchants/{merchant_id}', [MerchantController::class, 'update']);
    Route::delete('merchants/{merchant_id}', [MerchantController::class, 'destroy']);
});

// api/v1/products
Route::group([
    'prefix' => 'v1',
    'namespace' => 'App\Http\Controllers\Api\V1'
], function() {
    Route::get('products/{product_id}', [ProductController::class, 'show']);
});

// api/v1/products-categories
Route::group([
    'prefix' => 'v1',
    'namespace' => 'App\Http\Controllers\Api\V1'
], function() {
    Route::get('product_categories', [ProductCategoryController::class, 'index']);
});
