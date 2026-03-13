<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\DashBoardController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\StockController;
use App\Http\Controllers\Api\V1\CustomerController;   // ADDED
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout',[AuthController::class,'logout']);

        // Dashboard
        Route::get('/dashboard/summary',[DashboardController::class,'summary']);

        // Category Routes
        Route::get('/categories',[CategoryController::class,'index']);
        Route::post('/categories',[CategoryController::class,'store']);
        Route::get('/categories/{id}',[CategoryController::class,'show']);
        Route::put('/categories/{id}',[CategoryController::class,'update']);
        Route::delete('/categories/{id}',[CategoryController::class,'destroy']);

        // Product Routes
        Route::get('/products',[ProductController::class,'index']);
        Route::post('/products',[ProductController::class,'store']);
        Route::get('/products/{id}',[ProductController::class,'show']);
        Route::put('/products/{id}',[ProductController::class,'update']);
        Route::delete('/products/{id}',[ProductController::class,'destroy']);

        // Customer Route (FOR POS DROPDOWN)
        Route::get('/customers',[CustomerController::class,'index']);

        // Stock Routes
        Route::get('/stocks',[StockController::class,'index']);
        Route::post('/stocks',[StockController::class,'stockIn']);
        Route::post('/stocks/adjustment',[StockController::class,'stockAdjustment']);

        // Invoice Routes
        Route::get('/invoices',[InvoiceController::class,'index']);
        Route::post('/invoices',[InvoiceController::class,'store']);
        Route::get('/invoices/{id}',[InvoiceController::class,'show']);
        Route::put('/invoices/{id}',[InvoiceController::class,'update']);
        Route::delete('/invoices/{id}',[InvoiceController::class,'destroy']);

    });

});