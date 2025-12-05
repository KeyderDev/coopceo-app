<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// === Controladores ===
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\RegistroCompraController;
use App\Http\Controllers\Api\CashReconciliationController;
use App\Http\Controllers\Api\CustomEmailController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Misc\LogController;
use App\Http\Controllers\GananciasController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\ProductCuadreController;
use App\Http\Controllers\CalendarNoteController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DrawerController;
use App\Http\Controllers\DatabaseController;

// ==========================
// 🔹 RUTAS DE AUTENTICACIÓN
// ==========================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/otp', [PasswordController::class, 'sendOtp']);
Route::post('/password/verify-otp', [PasswordController::class, 'verifyOtp']);
Route::post('/password/reset', [PasswordController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/database/info', [DatabaseController::class, 'info']);
Route::get('/database/backup', [DatabaseController::class, 'backup']);
// ==========================
// 🔹 RUTAS DE USUARIOS
// ==========================
Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});


// ==========================
// 🔹 PRODUCTOS
// ==========================
Route::middleware('auth:api')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'updateStock']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/products/cuadre', [ProductCuadreController::class, 'store']);
});

// ==========================
// 🔹 DRAWERS
// ==========================

Route::middleware('auth:api')->group(function () {
    Route::get('/drawers', [DrawerController::class, 'index']);
    Route::post('/drawers/assign', [DrawerController::class, 'assign']);
    Route::get('/my-drawer', [DrawerController::class, 'myDrawer']);
    Route::post('/drawers/unassign', [DrawerController::class, 'unassign']);
});


// ==========================
// 🔹 VENTAS
// ==========================
Route::middleware('auth:api')->group(function () {
    Route::get('/sales', [SaleController::class, 'index']);
    Route::post('/sales', [SaleController::class, 'store']);
    Route::get('/my-transactions', [SaleController::class, 'myTransactions']);
    Route::get('/sales/{id}', [SaleController::class, 'show']);
});


// ==========================
// 🔹 COMPRAS (RegistroCompra)
// ==========================
Route::middleware('auth:api')->group(function () {
    Route::get('/compras', [RegistroCompraController::class, 'index']);
    Route::post('/compras', [RegistroCompraController::class, 'store']);
    Route::get('/compras/{id}', [RegistroCompraController::class, 'show']);
    Route::put('/compras/{id}', [RegistroCompraController::class, 'update']);
    Route::delete('/compras/{id}', [RegistroCompraController::class, 'destroy']);
});


// ==========================
// 🔹 RECONCILIACIONES DE CAJA
// ==========================
Route::get('/sales-reconcilliation', [CashReconciliationController::class, 'totalSales']);
Route::post('/cash-reconciliations', [CashReconciliationController::class, 'store']);
Route::middleware('auth:api')->get('/cash-reconciliations', [CashReconciliationController::class, 'index']);


// ==========================
// 🔹 REPORTES Y GANANCIAS
// ==========================
Route::middleware('auth:api')->get('/ganancias', [GananciasController::class, 'index']);
Route::get('/sales/resumen-mensual/{mes}', [MonthlyReportController::class, 'resumenMensual']);


// ==========================
// 🔹 CALENDARIO / HORARIOS
// ==========================
Route::get('/users', [ScheduleController::class, 'users']);
Route::post('/schedules', [ScheduleController::class, 'store']);
Route::post('/schedules/send-email', [ScheduleController::class, 'sendEmail']);

Route::middleware('auth:api')->group(function () {
    Route::get('/calendar-notes', [CalendarNoteController::class, 'index']);
    Route::post('/calendar-notes', [CalendarNoteController::class, 'store']);
    Route::delete('/calendar-notes/{date}', [CalendarNoteController::class, 'destroy']);
});


// ==========================
// 🔹 CORREOS PERSONALIZADOS
// ==========================
Route::middleware('auth:api')->post('/send-email', [CustomEmailController::class, 'sendCustomEmail']);


// ==========================
// 🔹 LOGS
// ==========================
Route::middleware('auth:api')->get('/logs', [LogController::class, 'index']);


// ==========================
// 🔹 UTILIDADES
// ==========================
Route::get('/ping', fn() => response()->json(['pong' => true]));
