<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MotorFormController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [AuthController::class, 'index'])->name('home');
Route::get('/motor-form', [MotorFormController::class, 'index'])->name('motor-form');
