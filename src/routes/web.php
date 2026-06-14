<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\FavoriteController;

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
Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/register',[AuthController::class,'create']);
Route::post('/register',[AuthController::class,'store']);

Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

Route::middleware('auth')->group(function () {
    Route::get('/mypage',[UserController::class,'mypage'])->name('mypage');
    Route::get('/mypage/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [UserController::class, 'updateAll'])->name('profile.update.all');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // いいね登録
    Route::post('/item/{item}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    // いいね解除
    Route::delete('/item/{item}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    // コメント送信用のルートを追加
    Route::post('/item/{item}/comment', [ItemController::class, 'storeComment'])->name('comments.store');

    Route::get('/sell',[SellController::class,'sell']);

        // 出品画面を表示する（GET）
    Route::get('/sell', [SellController::class, 'sell'])->name('sell');

    // ★【追加】画像選択時のリロードや、最終保存を受け取る（POST）
    // コントローラーの「store」メソッドにデータを送るようにします
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

});





/*
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');

    Route::patch('/index', [UserController::class, 'update']);
    Route::post('/profile/image', [UserController::class, 'updateImage'])->name('profile.image.update');

    Route::get('/mypage',[UserController::class,'mypage'])->name('mypage');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');


Route::get('/',[ItemController::class,'index']);
Route::get('/profile',[UserController::class,'profile']);

Route::get('/sell',[SellController::class,'sell']);
Route::get('/item',[ItemController::class,'item']);
Route::get('/purchase',[PurchaseController::class,'purchase']);
Route::get('/purchase/address',[AddressController::class,'edit']);
*/