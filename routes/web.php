<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\CharacterController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/consumir-api', [APIController::class, 'consumirAPI'])->name('api.consumir');

Route::post('/characters/store', [CharacterController::class, 'store']);
