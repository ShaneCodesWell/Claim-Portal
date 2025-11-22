<?php

use App\Http\Controllers\MotorFormController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [MotorFormController::class, 'index'])->name('home');
