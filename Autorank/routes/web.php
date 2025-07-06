<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController; //Page Controller import

// Route for the initial page load, index page load
Route::get('/', [PageController::class, 'home'])->name('home');

// Route for Page 1
Route::get('/page-1', [PageController::class, 'page1'])->name('page1');

// Route for Page 2
Route::get('/page-2', [PageController::class, 'page2'])->name('page2');
