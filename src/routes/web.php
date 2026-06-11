<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\PurchaseController;
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

Route::get('/login',[AuthController::class,'loginView'])->name('login');
Route::post('/login',[AuthController::class,'login']);
Route::get('/mypage',[UserController::class,'mypage'])->name('mypage');

Route::get('/register',[AuthController::class,'create']);
Route::post('/register',[AuthController::class,'store']);
Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile', [UserController::class, 'profile'])->name('profile');
    Route::patch('/mypage/profile', [UserController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});





/*
Route::get('/',[ItemController::class,'index']);
Route::get('/profile',[UserController::class,'profile']);

Route::get('/sell',[SellController::class,'sell']);
Route::get('/item',[ItemController::class,'item']);
Route::get('/purchase',[PurchaseController::class,'purchase']);
Route::get('/purchase/address',[AddressController::class,'edit']);
*/