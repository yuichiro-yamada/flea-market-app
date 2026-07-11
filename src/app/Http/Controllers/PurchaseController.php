<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\SalesRecord;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest; 

class PurchaseController extends Controller
{
    // 購入画面の表示
    public function showPurchase(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);     // 商品情報を取得、なければエラーを返す
        $user = Auth::user(); // ログインユーザー情報を取得

        // ステータスが「販売中」以外なら注文できない旨を返す
        if($item->sales_status !== 1 ){
            return redirect()->route('items.show', $item->id)->with('modal_message', "この商品は既に購入されています");
        }
        // DBに送付先住所があれば設定、なければユーザーの登録住所を設定
        $postcode = $user->shipping_postcode ?? $user->postcode;
        $address = $user->shipping_address ?? $user->address;
        $building = $user->shipping_postcode ? $user->shipping_building : $user->building;

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

// 支払い方法を即時更新する（PATCH /purchase/{item_id}
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
    public function updateAddress(AddressRequest $request, $item_id)
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
}
