<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('templates.index');
});

Route::get('/administrador', function () {
    return view('templates.administrador');
});
