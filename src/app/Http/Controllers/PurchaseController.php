<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\SalesRecord;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 💡 購入画面の表示（修正）
    public function showPurchase($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user(); // ログインユーザー情報を取得

        // 💡 セッションに変更後の住所があればそれを使い、なければユーザー登録時の住所を使う
        // （※Userモデルのカラム名「postcode」「address」「building」はご自身の環境に合わせて調整してください）
        $postcode = session('shipping_postcode', $user->postcode);
        $address = session('shipping_address', $user->address);
        $building = session('shipping_building', $user->building);

        return view('purchases.purchase', compact('item', 'postcode', 'address', 'building'));
    }

    // 💡 【追加】住所変更画面を表示
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('purchases.address', compact('item'));
    }

    // 💡 【追加】変更された住所をセッションに一時保存して、購入画面に戻る
    public function updateAddress(Request $request, $item_id)
    {
        // フォームから送られてきた値をセッション（一時記憶）に保存
        session([
            'shipping_postcode' => $request->postcode,
            'shipping_address'  => $request->address,
            'shipping_building' => $request->building,
        ]);

        // 購入画面（purchase.show）にリダイレクト
        return redirect()->route('purchase.show', $item_id);
    }

    // 💡 購入確定処理
    public function storePurchase(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // 1. 支払い方法の文字列を数値(ID)に変換（例：コンビニ=1, クレカ=2 ※実際のIDに合わせて調整してください）
        $paymentMethodId = $request->payment_method === 'クレジットカード支払い' ? 2 : 1;

        // 2. sales_recordsテーブルに新規レコードを追加
SalesRecord::create([
    'item_id'           => $item->id,
    'seller_id'         => $item->seller_id, 
    'buyer_id'          => $user->id,
    'payment_method'    => $paymentMethodId, 
    'purchase_price'    => $item->item_price, 
    'shipping_postcode' => session('shipping_postcode', $user->postcode),
    'shipping_address'  => session('shipping_address', $user->address),
    'shipping_building' => session('shipping_building', $user->building),
]);

        // 3. itemsテーブルの販売状態（sales_status）を 3 に変更
        $item->update([
            'sales_status' => 3
        ]);

        // 4. セッションの一時的な住所情報をクリア
        session()->forget(['shipping_postcode', 'shipping_address', 'shipping_building']);

        // 5. 商品詳細画面へリダイレクト（同時に「購入完了フラグ」をセッションに持たせる）
        return redirect()->route('items.show', $item->id)->with('purchase_completed', true);
    }
}
