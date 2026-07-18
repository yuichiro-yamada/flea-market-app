<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Item;
use App\Models\SalesRecord;
use Illuminate\Support\Facades\Auth;
use App\Services\PurchaseService;

class PaymentController extends Controller
{
    // PurchaseService（DB処理をまとめたクラス）をコントローラで使えるように受け取るためのコンストラクタ
    // /app/Services/PurchaseService.php
    public function __construct(
        private PurchaseService $purchaseService
    ) {
    }

    public function checkout(Request $request)
    {
        // 1. Stripeのシークレットキーを設定
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // ２. 支払い方法の設定
        $paymentTypes = ['card'];

        if ((int)$request->payment_method === 1) {
            $paymentTypes = ['konbini'];
        }

         // 3. 商品情報・ユーザー情報取得
        $item = Item::findOrFail($request->item_id);
        $user = Auth::user();

        // ４。　売り切れチェック（販売中でなければStripe決済画面へ遷移せず、戻ってエラーモーダル表示）
        if ($item->sales_status !== 1) {
            return back()->with('error', 'この商品は既に購入されています');
        }

        // PHPunitによるテストならStripeを利用せず、直接DB書き込み
        if (app()->environment('testing')) {
            if((int)$request->payment_method ===1){
                $this->purchaseService->reservePurchase(
                    $item->id,
                    $user,
                    (int)$request->payment_method
                );
            } else {
                $this->purchaseService->completePurchase(
                    $item->id,
                    $user,
                    (int)$request->payment_method
                );
            }
            return redirect('/mypage?page=buy');
        }

        // PHPunitによるテストではないならStripeを利用
        $session = Session::create([
            'payment_method_types' => $paymentTypes,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->item_name,
                    ],
                    'unit_amount' => $item->item_price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'item_id' => $request->item_id,
                'buyer_id' => Auth::id(),
            ],
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        // Stripeのセッション情報を取得（商品名を取得するために使う）
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = Session::retrieve($sessionId);

        // メタデータから商品IDを取り出し、商品名を取得する
        $item = Item::findOrFail($session->metadata->item_id);

        $salesRecord = null;
        $retry = 5;

        while ($retry--) {
            // sales_recordsテーブルにレコードがあるか確認
            $salesRecord = SalesRecord::where('item_id', $item->id)->first();

            if ($salesRecord) {
                break;
            }

            // Webhookでsales_recordsが作成されるまで最大1.5秒(0.3秒　X　５回)待機する
            usleep(300000);
        }

        // ケース1：レコードがなかった場合
        if (!$salesRecord) {
        return redirect()->route('mypage', ['page' => 'buy'])
            ->with('modal_message', "申し訳ございません\n何らかの原因で\n{$item->item_name}\nの購入に失敗しました");
        }

        // ケース２：レコードはあって購入者が自分だった場合
        if ($salesRecord->buyer_id === auth()->id()) {
            return redirect()->route('mypage', ['page' => 'buy'])
                ->with('modal_message', "{$item->item_name}\nの購入が完了しました！");
        }

        // ケース１・２にあてはまらなかった場合
        return redirect()->route('mypage', ['page' => 'buy'])
            ->with('modal_message', "申し訳ございません\n{$item->item_name}\nは他の方に購入されてしまいました");
    }

    // 決済中止時の処理（今回のケースではほぼ発生しないが念のため）
    public function cancel()
    {
        return redirect()->route('mypage', ['page' => 'buy'])
            ->with('modal_message', "{$item->item_name}\nの購入を中止しました");
    }
}


