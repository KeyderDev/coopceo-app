<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\CashReconciliationController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->get('/user', [UserController::class, 'me']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->get('/users', [UserController::class, 'index']);
Route::get('/logs', [LogController::class, 'index'])->middleware('auth:api');
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

Route::middleware('auth.api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});

Route::middleware('auth:api')->get('/my-transactions', [SaleController::class, 'myTransactions']);

Route::middleware('auth:api')->group(function () {
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
Route::middleware('auth:api')->group(function () {
    Route::get('/sales', [SaleController::class, 'index']);   // Listar ventas (opcional)
    Route::post('/sales', [SaleController::class, 'store']);  // Registrar venta
});

Route::get('/sales-reconcilliation', [CashReconciliationController::class, 'totalSales']);
Route::post('/cash-reconciliations', [CashReconciliationController::class, 'store']);
Route::middleware('auth:api')->get('/cash-reconciliations', [CashReconciliationController::class, 'index']);

