<?php

use App\Http\Controllers\JumpController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::get('/j/{code}', [JumpController::class, 'show']);
