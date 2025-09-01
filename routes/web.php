<?php
use Illuminate\Support\Facades\Route;

// Página principal
Route::get('/', function () {
    return view('portal'); // Página inicial con botones
});

// SPA Admin
Route::get('/admin-panel/{any?}', function () {
    return view('admin'); // admin.blade.php
})->where('any', '.*');

// SPA User
Route::get('/user-panel/{any?}', function () {
    return view('user'); // Blade que monta SPA user
})->where('any', '.*');

