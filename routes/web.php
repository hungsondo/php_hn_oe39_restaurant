<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AdminController::class, 'index']);
Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::resource('books', BookController::class);
Route::get('language/{lang}', [App\Http\Controllers\HomeController::class, 'changeLanguage'])->name('change-language');
