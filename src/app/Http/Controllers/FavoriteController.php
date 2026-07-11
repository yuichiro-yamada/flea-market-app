<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // いいね登録
    public function store(Item $item)
    {
        // ログイン中のユーザーIDとこの商品IDを中間テーブルに紐づける
        Auth::user()->favoriteItems()->syncWithoutDetaching([$item->id]);

        // 直前の画面（商品詳細画面）にリダイレクトして再表示
        return back();
    }

    // いいね解除
    public function destroy(Item $item)
    {
        // 中間テーブルから紐づきを解除
        Auth::user()->favoriteItems()->detach($item->id);

        return back();
    }
}
