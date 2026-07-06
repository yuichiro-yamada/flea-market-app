<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Item;
use App\Models\SalesRecord;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        // 1. Stripeのシークレットキーを設定
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // 1. 支払い方法の初期値を設定
        $paymentTypes = ['card']; // デフォルトはクレジットカード
        if ($request->payment_method === 'konbini') {
            $paymentTypes = ['konbini'];
        }

         // 3. セッションの作成
        $session = Session::create([
            'payment_method_types' => $paymentTypes, // ここに変数を入れる
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $request->product_name,
                    ],
                    'unit_amount' => $request->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'item_id' => $request->item_id, // 画面から送られてきた商品のID
                'buyer_id' => Auth::user()->id, // 💡購入手続き中のユーザーIDを金庫に預ける
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

        // 商品詳細画面へリダイレクト（同時に「購入完了フラグ」をセッションに持たせる）
        // 購入完了フラグをセッションに持たせることで購入画面から商品詳細画面へ遷移した(購入した)直後であることを伝える
        // このセッション情報は次ページを標示し終わった後、削除される（フラッシュデータ）
        return redirect()->route('mypage', ['page' => 'buy'])
            ->with('modal_message', "{$item->item_name}\nの購入が完了しました！");
    }

    public function cancel()
    {
        // 決済キャンセル時の処理
        return view('payment.cancel');
    }
}
