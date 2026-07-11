<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use App\Models\SalesRecord;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function reservePurchase(int $itemId, User $user)
    {
        DB::transaction(function () use ($itemId, $user) {

            $item = Item::lockForUpdate()->findOrFail($itemId);

            if ($item->sales_status !== 1) {
                throw new \Exception('商品は既に購入されています');
            }

            $item->update([
                'sales_status' => 2,
                'buyer_id' => $user->id,
            ]);

            // 時保存していたuserテーブルのお届け先住所情報をクリア
            $user->shipping_postcode = null;
            $user->shipping_address = null;
            $user->shipping_building = null;
            $user->save();
        });
    }


    // 決済完了処理共通処理（商品情報は商品idのみを取得）
    public function completePurchase(int $itemId, User $user, int $paymentMethodId)
    {
        \Log::info('completePurchase開始');
        // sales_recordsテーブルに新規レコードを追加

        DB::transaction(function () use ($itemId, $user, $paymentMethodId) {

            // 最新の商品の状態をロックして取得し直す
            $item = Item::lockForUpdate()->findOrFail($itemId);

            // 既に購入済みなら終了
            if ($item->sales_status !== 1) {
                throw new \Exception('商品は既に購入されています');
            }

            SalesRecord::create([
                'item_id'           => $item->id,
                'seller_id'         => $item->seller_id,
                'buyer_id'          => $user->id,
                'payment_method'    => $paymentMethodId,
                'purchase_price'    => $item->item_price,
                'shipping_postcode' => $user->shipping_postcode ?? $user->postcode,
                'shipping_address'  => $user->shipping_address ?? $user->address,
                'shipping_building' => $user->shipping_building ?? $user->building,
            ]);

            \Log::info('SalesRecord保存成功');

            // itemsテーブルの販売状態を3(SOLD OUT)へ変更
            $item->update([
                'sales_status' => 3,
                'buyer_id'     => $user->id,
            ]);

            // 一時保存していたuserテーブルのお届け先住所情報をクリア
            $user->shipping_postcode = null;
            $user->shipping_address = null;
            $user->shipping_building = null;
            $user->save();
        });
    }
}