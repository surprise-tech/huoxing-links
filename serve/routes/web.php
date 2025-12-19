<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Install\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [Controller::class, 'welcome']);
Route::get('/j/{code}', [HomeController::class, 'show']);

Route::any('/install', [IndexController::class, 'index']);
