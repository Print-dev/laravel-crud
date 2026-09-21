<?php

use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

/* RUTAS CON MIDDLEWARE DE ADMINISTRADOR */
/* Route::middleware(['auth','admin'])->group(function () { // el admin sale de la validacio de IsAdmin.php que se agrego en bootstrap/app.php
    Route::get('/admin/dashboard', function () {
        return 'panel de administrador';
    })->name('admin.dashboard');

    Route::get('/admin/users', function () {
        return 'panel de usuarios';
    })->name('admin.users');
}); */

Route::middleware(['auth', 'admin'])
    ->prefix('admin') // prefix/dashboard 
    ->name('admin.') // name.dashboard
    ->group(function () {    
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
    });

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout(); // cierra y destruye la sesión del usuario autenticado en la aplicación web
    $request->session()->invalidate(); // invalida la sesión actual del usuario, eliminando todos los datos asociados a ella
    $request->session()->regenerateToken(); // genera un nuevo token CSRF para la sesión, lo que ayuda a prevenir ataques de falsificación de solicitudes entre sitios (CSRF)
    return redirect('/');
})->name('logout');