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

    /**
     * ⭕ 1. 画像の一時保存専用メソッド（通常の Request を使用）
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'item_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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

    /**
     * ⭕ 2. 本番の出品保存専用メソッド（作成した SellRequest をそのまま使える！）
     */
    public function store(SellRequest $request) // 👈 手動のバリデーション記述は一切不要になります
    {
        // 基本的な商品情報を保存
        $item = Item::create([
            'item_name'   => $request->input('item_name'),
            'seller_id'   => Auth::id(),
            'condition'   => $request->input('condition'),
            'brand_name'  => $request->input('brand_name'),
            'item_detail' => $request->input('item_detail'),
            'item_price'  => $request->input('item_price'),
            'item_image'  => 'no_image.png', 
            'sales_status' => 1,
        ]);

        // 一時保存された画像がある場合、本番フォルダへ移動
        if ($request->has('item_tmp_image_path') && $request->input('item_tmp_image_path') != '') {
            $tmpPath = $request->input('item_tmp_image_path');
            
            // ファイル名を取得する際、パスに「tmp/」が含まれていても綺麗にファイル名だけを切り出します
            $fileName = time() . '_' . basename($tmpPath);
            $newPath = 'images/items/' . $fileName;

            // Storage::disk('public')->exists() が判定に失敗するのを防ぐため、より確実な方法に変更
            if (Storage::disk('public')->exists($tmpPath)) {
                // 先に本番用のディレクトリ（images/items）が存在するか確認し、なければ自動作成
                if (!Storage::disk('public')->exists('images/items')) {
                    Storage::disk('public')->makeDirectory('images/items');
                }

                // ファイルを本番フォルダへ移動
                Storage::disk('public')->move($tmpPath, $newPath);
                
                // データベースの商品画像名を更新して保存
                $item->item_image = $fileName;
                $item->save(); 
                
                \Log::info('【成功】画像を移動し、DBを上書きしました: ' . $fileName);
            } else {
                // もし画像移動に失敗した場合は、ログに原因を出力します
                \Log::error('【エラー】tmpフォルダにファイルが見つかりません: ' . $tmpPath);
            }
        }

        // カテゴリIDの紐付け
        if ($request->has('category_ids')) {
            $item->categories()->attach($request->input('category_ids'));
        }

        return redirect()->route('mypage')->with('success', '商品を出品しました！');
    }
}
