<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Item;
use App\Models\User;
use App\Services\PurchaseService;

class WebhookController extends Controller
{
    public function handleStripeWebhook(Request $request)
    {
        // StripeのAPIキーを設定
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Stripeの秘密鍵を使って通信を準備
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Stripeから送信されたWebhookイベントを検証
        // 決済に使われた方法を判定
        // クレジットカード（card）なら 0、コンビニ決済（konbini）なら 1 に変換
        $session = $event->data->object;
        $paymentMethodStr = $session->payment_method_types[0] ?? null;
        $paymentMethodId = match ($paymentMethodStr) {
            'card'    => 0,
            'konbini' => 1,
            default   => null,
        };

        // Stripeから商品IDと購入者IDを取り出す
        $item_id = $session->metadata->item_id;
        $buyer_id = $session->metadata->buyer_id;
        // 購入者IDを元にDBから購入者情報を取得
        $user = User::findOrFail($buyer_id);

        $purchaseService = new PurchaseService();

        // Stripeの画面で「購入ボタン」を押した際に実行された時
        if ($event->type === 'checkout.session.completed') {

            // お金を払っていない（unpaid＝コンビニ決済）場合
            if ($session->payment_status === 'unpaid') {

                $purchaseService->reservePurchase($item_id, $user);

                return response()->json(['status' => 'waiting']);

            } else {      // クレジットカード決済の場合（paid）

                // app/Services/PurchaseService.phpのompletePurchaseを呼び出してDB保存
                $purchaseService->completePurchase($item_id, $user, $paymentMethodId);

                return response()->json(
                    ['status' => 'success'],200);
            }
        }

        // 後からお金が支払われた時（コンビニ決済実行3分後）
        if ($event->type === 'checkout.session.async_payment_succeeded') {
            \Log::info('async_payment_succeeded');

            // app/Services/PurchaseService.phpのompletePurchaseを呼び出してDB保存
            $purchaseService->completePurchase($item_id, $user, $paymentMethodId);

            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['status' => 'success']);
    }
}