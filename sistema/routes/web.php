<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

// Gestión
Route::resource('customers', CustomerController::class)->except('show');
Route::resource('products', ProductController::class)->except('show');

// Configuraciones
Route::resource('roles', RoleController::class)->except('show');
Route::resource('users', UserController::class)->except('show');
Route::resource('categories', CategoryController::class)->except('show');

Route::view('/login', 'auth.login')->name('login');
