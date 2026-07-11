<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Review;

class ItemController extends Controller
{
public function index(Request $request)
{
    $query = Item::whereIn('sales_status', [1, 2, 3]);

    // ログインしている場合、自分の出品商品は表示しない
    if (Auth::check()) {
        $query->where('seller_id', '!=', Auth::id());
    }
    // 検索キーワードがある場合、部分一致で絞り込む
    // ヘッダーの name="keyword" の値を受け取ります
    if ($request->filled('keyword')) {
        $keyword = $request->query('keyword');
        $query->where('item_name', 'LIKE', "%{$keyword}%");
    }

    // URLに ?tab=mylist がある場合の処理（ログイン・未ログイン両方対応）
    if ($request->query('tab') === 'mylist') {
        if (Auth::check()) {
            $user = Auth::user();
            // ログインユーザーがお気に入りした商品だけに絞り込む
            $query->whereHas('favoritedByUsers', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            $items = $query->get();
            return view('index', compact('user', 'items'));
        } else {
            // 未ログインの場合：お気に入りはないので、空のコレクション（データなし）にして返す
            $items = collect();
            return view('index', compact('items'));
        }
    }

    // 通常時（おすすめタブ）の処理
    $items = $query->get();
    
    if (Auth::check()) {
        $user = Auth::user();
        return view('index', compact('user', 'items'));
    }
    return view('index', compact('items'));
}


    public function show(Item $item){
        // 商品（$item）に紐づいている「カテゴリ一覧」と「レビュー一覧」のデータをDBからまとめて取得
        $item->load(['categories', 'reviews']);
        // 「レビューの総数」と「いいね（お気に入り）したユーザーの総数」をDBに計算させ、その数字を $item の中にセット
        $item->loadCount(['reviews', 'favoritedByUsers']);
        // 「カテゴリ」「レビュー一覧」「それぞれの件数」を含んだ変数 $item をbladeに送る
        return view('items/item', compact('item'));
    }
    public function storeComment(ReviewRequest $request, Item $item)
    {
        // reviewsテーブルにデータを登録
        // Item.php に定義されている reviews() リレーションを利用
        $item->reviews()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return back()->with('modal_message', 'コメントを投稿しました');
    }
}
