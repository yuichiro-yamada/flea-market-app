<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SellRequest; 


class SellController extends Controller
{
    public function sell(){
        $categories = Category::all();
        return view('items.sell',compact('categories'));
    }

    // 出品画面を表示するメソッド
    //public function create()
    //{
        // 既存のカテゴリ取得ロジックなどをここに
        // $categories = Category::all();
    //    return view('sell.create'); 
    //}

    // 出品・統合保存メソッド
    public function store(SellRequest $request)
    {
        // パターンA：画像が選択されて自動リロード（一時保存）が入ったとき
        if ($request->input('action') === 'select_image') {


            if ($request->hasFile('item_image')) {
                $file = $request->file('item_image');
                $originalName = $file->getClientOriginalName();
                $tmpPath = $file->store('tmp', 'public');

                return redirect()->back()
                    ->withInput() 
                    ->with([
                        'item_tmp_image_name' => $originalName,
                        'item_tmp_image_path' => $tmpPath
                    ]);
            }
            return redirect()->back()->withInput();
        }

        // パターンB：一番下の「出品する（保存）」ボタンが押されたとき
        if ($request->input('action') === 'save_all') {

            // 2. 基本的な商品情報を一度に保存（UserControllerの $user->update() と同じ動き）
            $item = Item::create([
                'item_name'   => $request->input('item_name'),
                'brand_name'  => $request->input('brand_name'),
                'seller_id'   => Auth::id(),
                'condition'   => $request->input('condition'),
                'item_detail' => $request->input('item_detail'),
                'item_price'  => $request->input('item_price'),
                'item_image'  => 'no_image.png', // ひとまず初期値を入れておく
                'sales_status' => 1,
            ]);

            // 3. 一時保存された画像がある場合、本番フォルダへ移動して上書き保存
            // ⚠️ filled() から has() に変更し、確実に値をキャッチします
            if ($request->has('item_tmp_image_path') && $request->input('item_tmp_image_path') != '') {
                $tmpPath = $request->input('item_tmp_image_path');
                $fileName = time() . '_' . basename($tmpPath);
                $newPath = 'images/items/' . $fileName;

                // publicディスクに一時ファイルが存在するか確認
                if (Storage::disk('public')->exists($tmpPath)) {
                    // ファイルを移動
                    Storage::disk('public')->move($tmpPath, $newPath);
                    
                    // 画像名を上書きして、もう一度保存する
                    $item->item_image = $fileName;
                    $item->save(); 
                    
                    \Log::info('【成功】画像を移動し、DBを上書きしました: ' . $fileName);
                } else {
                    // フォルダにはあるのにここを通る場合、パスの形式（先頭の「/」など）がズレています
                    \Log::error('【エラー】tmpフォルダにファイルが見つかりません: ' . $tmpPath);
                }
            } else {
                \Log::info('【通知】item_tmp_image_path がリクエストに含まれていません。');
            }

            // 4. カテゴリIDの紐付け（中間テーブル）
            if ($request->has('category_ids')) {
                $item->categories()->attach($request->input('category_ids'));
            }

            // 出品完了後、マイページへリダイレクト
            return redirect()->route('mypage')->with('success', '商品を出品しました！');
        }
    }
}
