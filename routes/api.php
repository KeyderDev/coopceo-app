<?php

use Illuminate\Support\Facades\Route;

Route::group([], function () {

    Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);

    Route::post('/password/otp', [\App\Http\Controllers\Api\PasswordController::class, 'sendOtp']);
    Route::post('/password/verify-otp', [\App\Http\Controllers\Api\PasswordController::class, 'verifyOtp']);
    Route::post('/password/reset', [\App\Http\Controllers\Api\PasswordController::class, 'resetPassword']);
    // Route::middleware(['tenant', 'apitoken'])->group(function () {

    Route::middleware(['apitoken'])->group(function () {
        Route::post('/user/profile-picture', [\App\Http\Controllers\Api\UserController::class, 'updateProfilePicture']);


        Route::get('/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'index']);
        Route::post('/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'store']);

        Route::get('/database/info', [\App\Http\Controllers\DatabaseController::class, 'info']);
        Route::get('/database/backup', [\App\Http\Controllers\DatabaseController::class, 'backup']);

        Route::get('/user', [\App\Http\Controllers\Api\UserController::class, 'me']);
        Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);

        Route::get('/users', [\App\Http\Controllers\Api\UserController::class, 'index']);
        Route::get('/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'show']);
        Route::put('/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'update']);
        Route::delete('/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'destroy']);

        Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
        Route::post('/products', [\App\Http\Controllers\Api\ProductController::class, 'store']);
        Route::put('/products/{product}', [\App\Http\Controllers\Api\ProductController::class, 'updateStock']);
        Route::delete('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'destroy']);
        Route::post('/products/cuadre', [\App\Http\Controllers\ProductCuadreController::class, 'store']);

        Route::get('/drawers', [\App\Http\Controllers\DrawerController::class, 'index']);
        Route::post('/drawers/assign', [\App\Http\Controllers\DrawerController::class, 'assign']);
        Route::get('/my-drawer', [\App\Http\Controllers\DrawerController::class, 'myDrawer']);
        Route::post('/drawers/unassign', [\App\Http\Controllers\DrawerController::class, 'unassign']);

        Route::get('/sales', [\App\Http\Controllers\Api\SaleController::class, 'index']);
        Route::post('/sales', [\App\Http\Controllers\Api\SaleController::class, 'store']);
        Route::get('/my-transactions', [\App\Http\Controllers\Api\SaleController::class, 'myTransactions']);
        Route::get('/sales/{id}', [\App\Http\Controllers\Api\SaleController::class, 'show']);

        Route::get('/compras', [\App\Http\Controllers\Api\RegistroCompraController::class, 'index']);
        Route::post('/compras', [\App\Http\Controllers\Api\RegistroCompraController::class, 'store']);
        Route::get('/compras/{id}', [\App\Http\Controllers\Api\RegistroCompraController::class, 'show']);
        Route::put('/compras/{id}', [\App\Http\Controllers\Api\RegistroCompraController::class, 'update']);
        Route::delete('/compras/{id}', [\App\Http\Controllers\Api\RegistroCompraController::class, 'destroy']);

        Route::get('/cash-reconciliations', [\App\Http\Controllers\Api\CashReconciliationController::class, 'index']);
        Route::post('/cash-reconciliations', [\App\Http\Controllers\Api\CashReconciliationController::class, 'store']);

        Route::get('/calendar/users', [\App\Http\Controllers\ScheduleController::class, 'users']);
        Route::post('/schedules', [\App\Http\Controllers\ScheduleController::class, 'store']);
        Route::post('/schedules/send-email', [\App\Http\Controllers\ScheduleController::class, 'sendEmail']);

        Route::get('/calendar-notes', [\App\Http\Controllers\CalendarNoteController::class, 'index']);
        Route::post('/calendar-notes', [\App\Http\Controllers\CalendarNoteController::class, 'store']);
        Route::delete('/calendar-notes/{date}', [\App\Http\Controllers\CalendarNoteController::class, 'destroy']);

        Route::get('/ganancias', [\App\Http\Controllers\GananciasController::class, 'index']);

        Route::get('/sales/resumen-mensual/{mes}', [\App\Http\Controllers\MonthlyReportController::class, 'resumenMensual']);

        Route::get('/sales-reconcilliation', [\App\Http\Controllers\Api\CashReconciliationController::class, 'totalSales']);

        Route::get('/logs', [\App\Http\Controllers\Misc\LogController::class, 'index']);
    });
});
