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

// 商品一覧画面を表示する
Route::get('/', [ItemController::class, 'index'])->name('index');

// 商品詳細画面を表示する
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

Route::middleware(['auth', 'verified'])->group(function () {
    // マイページを表示する
    Route::get('/mypage',[UserController::class,'mypage'])->name('mypage');

    // プロフィール画面を表示する
    Route::get('/mypage/profile', [UserController::class, 'profile'])->name('profile');
    // プロフィール画面の修正内容をアップデートする
    Route::post('/profile/update', [UserController::class, 'updateAll'])->name('profile.update.all');

    // 商品詳細画面で「いいね」登録する
    Route::post('/item/{item}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    // 商品詳細画面で「いいね」解除する
    Route::delete('/item/{item}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    // 商品詳細画面でコメントを追加する
    Route::post('/item/{item}/comment', [ItemController::class, 'storeComment'])->name('comments.store');

    // 出品画面を表示する
    Route::get('/sell', [SellController::class, 'sell'])->name('sell');

    // 出品画面で商品画像を一時アップロードする
    Route::post('/sell/upload', [SellController::class, 'uploadImage'])->name('sell.upload');

    // 出品画面で「出品する」ボタンを押す
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // 商品購入画面を表示する
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchase'])->name('purchase.show');

    // 商品購入画面で支払い方法を変更する
    Route::patch('/purchase/{item_id}', [PurchaseController::class, 'updatePaymentMethod'])->middleware('auth');

    // 商品購入画面から住所変更画面へ遷移する
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('address.edit');
    // 住所変更画面から商品購入が面倒に戻る
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('address.update');

    // 商品購入画面で「購入する」ボタンを押す、Stripeによる決済開始（POST）
    Route::post('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    // 決済成功・キャンセル（GET）
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

// Breeze（auth.php）の中に
// ログイン画面（/login)と新規登録画面(/register）をログアウト（/logout）を
// 表示・処理するためのURLとルートが用意さされている
require __DIR__.'/auth.php';

// Stripeからの非同期通知を受け取るAPIエンドポイント
Route::post('/api/stripe/webhook', [WebhookController::class, 'handleStripeWebhook']);
