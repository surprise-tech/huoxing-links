<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\Install\IndexController;
use App\Http\Controllers\JumpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [Controller::class, 'welcome']);
Route::get('/j/{code}', [JumpController::class, 'show']);

Route::any('/install', [IndexController::class, 'index']);
