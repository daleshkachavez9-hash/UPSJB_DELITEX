<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

// Gestión
Route::resource('customers', CustomerController::class)->except('show');

// Configuraciones
Route::resource('roles', RoleController::class)->except('show');

Route::view('/login', 'auth.login')->name('login');
