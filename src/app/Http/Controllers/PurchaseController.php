<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\SalesRecord;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 購入画面の表示
    public function showPurchase(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);     // 商品情報を取得、なければエラーを返す
        $user = Auth::user(); // ログインユーザー情報を取得

        // ステータスが「販売中」以外なら注文できない旨を返す
        if($item->sales_status !== 1 ){
            return redirect()->route('items.show', $item->id)->with('purchase_error', true);
        }
        // DBに送付先住所があれば設定、なければユーザーの登録住所を設定
        $postcode = $user->shipping_postcode ?? $user->postcode;
        $address = $user->shipping_address ?? $user->address;
        $building = $user->shipping_postcode ? $user->shipping_building : $user->building;

        // ⭕ セッションを排除し、DBの配送先（shipping_*）か通常の登録住所かを綺麗に判定
        $postcode = $user->shipping_postcode ?? $user->postcode;
        $address  = $user->shipping_address  ?? $user->address;
        $building = $user->shipping_building ?? $user->building;

        // ⭕ データベースの更新処理（update）を削除し、変数への代入だけに整理します
        if ($request->has('payment_method')) {
            // パラメーターがあればそれを画面に渡す
            $payment_method = $request->payment_method;
        } else {
            // なければユーザーの現在の設定値を取得する（初期値は1）
            $payment_method = $user->default_payment_method ?? 1;
        }

        return view('purchases.purchase', compact('item', 'postcode', 'address', 'building', 'payment_method'));
    }

/**
 * 支払い方法を即時更新する（PATCH /purchase/{item_id}）
 */
public function updatePaymentMethod(Request $request, $item_id)
{
    // ログイン中のユーザーを取得
    $user = Auth::user();

    // バリデーション（0:クレカ、1:コンビニ のみ許可）
    $request->validate([
        'payment_method' => 'required|in:0,1',
    ]);

    // usersテーブルの default_payment_method カラムを更新
    $user->update([
        'default_payment_method' => $request->payment_method
    ]);

    // 元の購入画面にリダイレクト（選択された値を引き継ぐ）
    return redirect()->route('purchase.show', [
        'item_id' => $item_id, 
        'payment_method' => $request->payment_method
    ])->with('payment_method_updated', true);
}


    // 住所変更画面を表示
    public function editAddress($item_id)
    {
        return view('purchases.address', compact('item_id'));
    }

    // 変更された住所をDBに保存して、購入画面に戻る
    public function updateAddress(Request $request, $item_id)
    {
        // 1. ログイン中のユーザー情報を取得
        $user = Auth::user(); 

        // 2. users テーブルの新設カラムに値を保存（上書き）
        $user->update([
            'shipping_postcode'      => $request->shipping_postcode,
            'shipping_address'       => $request->shipping_address,
            'shipping_building'      => $request->shipping_building,
        ]);

        // 購入画面（purchase.show）にリダイレクト
        return redirect()->route('purchase.show', $item_id);
    }

    // 購入確定処理
    public function storePurchase(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // ステータスが「販売中」以外なら注文できない旨を返す
        if($item->sales_status !== 1 ){
            return redirect()->route('items.show', $item->id)->with('purchase_error', true);
        }

        // 1. 支払い方法の文字列を数値(ID)に変換（例：コンビニ=1, クレカ=2
        $paymentMethodId = $request->payment_method === 'クレジットカード支払い' ? 2 : 1;

        // 2. sales_recordsテーブルに新規レコードを追加
        SalesRecord::create([
            'item_id'           => $item->id,
            'seller_id'         => $item->seller_id, 
            'buyer_id'          => $user->id,
            'payment_method'    => $paymentMethodId, 
            'purchase_price'    => $item->item_price, 
            'shipping_postcode' => $user->shipping_postcode ?? $user->postcode,
            'shipping_address'  => $user->shipping_address  ?? $user->address,
            'shipping_building' => $user->shipping_building ?? $user->building,
        ]);

        // 3. itemsテーブルの販売状態（sales_status）を １(販売中)から3(SOLD OUT)へ変更
        $item->update([
            'sales_status' => 3
        ]);

        // 4. userテーブルのお届け先住所情報をクリア
        $user->shipping_postcode = null;
        $user->shipping_address = null;
        $user->shipping_building = null;
        $user->save();

        // 5. 商品詳細画面へリダイレクト（同時に「購入完了フラグ」をセッションに持たせる）
        // 購入完了フラグをセッションに持たせることで購入画面から商品詳細画面へ遷移した(購入した)直後であることを伝える
        // このセッション情報は次ページを標示し終わった後、削除される（フラッシュデータ）
        return redirect()->route('mypage', ['page' => 'buy'])->with('purchase_completed', true);
    }
}
