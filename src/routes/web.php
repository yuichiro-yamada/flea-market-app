<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommerceController;
use App\Http\Controllers\AddressController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[ItemController::class,'index']);
Route::get('/login',[AuthController::class,'login']);
Route::get('/register',[AuthController::class,'register']);
Route::get('/profile',[UserController::class,'profile']);
Route::get('/mypage',[UserController::class,'mypage']);
Route::get('/sell',[CommerceController::class,'sell']);
Route::get('/item',[ItemController::class,'item']);
Route::get('/purchase',[CommerceController::class,'purchase']);
Route::get('/purchase/address',[AddressController::class,'edit']);
