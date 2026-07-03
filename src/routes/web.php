<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;

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


Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage',[UserController::class,'mypage'])->name('mypage');
    Route::get('/mypage/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [UserController::class, 'updateAll'])->name('profile.update.all');

    // いいね登録
    Route::post('/item/{item}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    // いいね解除
    Route::delete('/item/{item}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    // コメント送信用のルートを追加
    Route::post('/item/{item}/comment', [ItemController::class, 'storeComment'])->name('comments.store');

    // 出品画面を表示する（GET）
    Route::get('/sell', [SellController::class, 'sell'])->name('sell');

    // 画像選択時のリロードや、最終保存を受け取る（POST）
    // コントローラーの「store」メソッドにデータを送るようにします
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // 画像一時アップロード専用のルートを新しく追加
    Route::post('/sell/upload', [SellController::class, 'uploadImage'])->name('sell.upload');

    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchase'])->name('purchase.show');
    // 💡 【追加】ボタンを押した時のPOSTルート（保存用）
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'storePurchase'])->name('purchase.store');

    // ルーティングを追加（コントローラー名はご自身の環境に合わせてください）
    Route::patch('/purchase/{item_id}', [PurchaseController::class, 'updatePaymentMethod'])->middleware('auth');

    // 💡 【追加】住所変更画面を表示するルート
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('address.edit');
    // 💡 【追加】変更された住所を一時保存して購入画面に戻るルート
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('address.update');

    // Stripeによる決済
    // 決済開始（POST）
    Route::post('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    // 決済成功・キャンセル（GET）
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

});

// Breeze（auth.php）の中に
// ログイン画面（/login)と新規登録画面(/register）をログアウト（/logout）を
// 表示・処理するためのURLとルートが用意さされている
require __DIR__.'/auth.php';

Route::post('/stripe/webhook', [WebhookController::class, 'handleStripeWebhook']);
Route::post('/api/stripe/webhook', [WebhookController::class, 'handleStripeWebhook']);
