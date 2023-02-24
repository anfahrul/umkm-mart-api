<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// api/v1/merchants
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function() {
    Route::apiResource('merchants', MerchantController::class);
    // Route::get('merchants/info?merchant_id={merchant_id}', 'MerchantController@show');
});

// api/v1/products
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function() {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('product_categories', ProductCategoryController::class);
});
