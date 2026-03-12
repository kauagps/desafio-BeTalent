<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    
    Route::apiResource('products', ProductController::class);

    Route::apiResource('clients', ClientsController::class);

    Route::post('/checkout', [TransactionsController::class, 'store']);

    Route::post('/logout', [AuthController::class, 'logout']);
});