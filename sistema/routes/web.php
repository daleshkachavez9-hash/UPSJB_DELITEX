<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/administrador', function () {
    return view('templates.administrador');
});

Route::get('/', function () {
    return view('templates.index');
});

