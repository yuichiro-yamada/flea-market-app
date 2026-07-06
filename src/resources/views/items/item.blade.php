@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/item.css') }}" />
@endsection

@section('content')
<div class="item__form">
    <div class="common__badge--position">
        <img src="/storage/images/items/{{ $item->item_image }}" class="item__picture--photo">
        @if ($item->sales_status == 2 && ($item->seller_id == Auth::id() || $item->buyer_id == Auth::id()))
            <div class="unsold-badge"><span>UNSD</span></div>
        @elseif ($item->sales_status == 3 || ($item->sales_status == 2 && $item->seller_id != Auth::id() && $item->buyer_id != Auth::id()))
            <div class="sold-badge"><span>SOLD</span></div>
        @endif
    </div>
    <div class="item__content">
        <h1>{{ $item->item_name }} </h1>
        <div class="item__content--brand">{{ $item->brand_name }}</div>
        <div class="item__content--price">
            <div class="item__content--price-unit">¥</div>
            <div class="item__content--price-tag">{{ $item->item_price_formatted }}</div>
            <div class="item__content--price-tax">(税込)</div>
        </div>
        <div class="item__content--mark">
            <div class="item__content--mark-unit">
                {{-- 💡 ログインしている場合 --}}
                @if($item->favoritedByUsers->contains(Auth::id()))
                    {{-- すでにいいねしている場合：クリックで「解除（DELETE）」 --}}
                    <form action="{{ route('favorites.destroy', $item) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                            <img src="/images/heart_pink.png" alt="いいね解除">
                        </button>
                    </form>
                @else
                    {{-- まだいいねしていない場合：クリックで「登録（POST）」 --}}
                    <form action="{{ route('favorites.store', $item) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                            <img src="/images/heart_default.png" alt="いいね登録">
                        </button>
                    </form>
                @endif
                {{-- いいねの総数を表示（loadCountを使うことで _count で取得できます） --}}
                <div class="item__content--mark-count">
                    {{ $item->favoritedByUsers_count ?? $item->favoritedByUsers->count() }}
                </div>
            </div>
            <div class="item__content--mark-unit">
                <a href="#anchor__comment">
                    <img src="/images/baloon.png">
                </a>
                <div class="item__content--mark-count">
                    {{ $item->reviews_count ?? $item->reviews->count() }}
                </div>
            </div>
        </div>
        @if($item->sales_status == 1)
            @if($item->seller_id != Auth::id()) )
                <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="common__button">
                    購入手続きへ
                </a>
            @else
                <button class="common__button--disabled" disabled>
                    出品者ご本人のためご注文できません
                </button>
            @endif
        @elseif ($item->sales_status == 2 && ($item->seller_id == Auth::id()))
            <button class="common__button--nopaid">
                購入されましたが入金はまだされていません
            </button>
        @elseif ($item->sales_status == 2 && ($item->buyer_id == Auth::id()))
            <button class="common__button--nopaid">
                コンビニエンスストアでの入金をお願いします
            </button>
        @elseif ($item->sales_status == 3 || ($item->sales_status == 2 && $item->seller_id != Auth::id() && $item->buyer_id != Auth::id()))
            <button class="common__button--disabled" disabled>
                売り切れました
            </button>
        @else
            <button class="common__button--disabled" disabled>
                こちらの商品はご注文できません
            </button>
        @endif

        <h2>商品説明</h2>
        <div>{{ $item->item_detail }}</div>
        <h2>商品の情報</h2>
        <div class="item__info--wrapper">
            <div class="item__info--title">カテゴリ</div>
            <div class="item__category--box">
            @foreach($item->categories as $category)
                <div class="item__category--select">{{ $category->category_name }}</div>
            @endforeach
            </div>
        </div>
        <div class="item__info--wrapper">
            <div class="item__info--title">商品の状態</div>
            <div class="item__info--content">{{ $item->condition_text }}</div>
        </div>
        {{-- 💡 1. タイトルとコメント数の表示 --}}
        <h2  id="anchor__comment">コメント ({{ $item->reviews_count }})</h2>

        {{-- 💡 2. コメント一覧の表示（すべてループして出力） --}}
        <div class="item__comment-list">
            @foreach($item->reviews as $review)
            <div class="item__comment--wrap">
                <div class="item__content--picture">
                    {{-- データベースに値があり、かつ public/storage 内にファイルが実在するかチェック --}}
                    @if(!blank($review->user->member_image) && file_exists(public_path('storage/images/profile/' . $review->user->member_image)))
                        <img src="/storage/images/profile/{{ $review->user->member_image }}" class="item__content--picture-photo">
                    @else
                        {{-- データがない、またはファイルがフォルダにない場合は初期画像を表示 --}}
                        <img src="/storage/images/profile/silver.png" class="item__content--picture-photo">
                    @endif
                    <div class="item__content--picture-name">{{ $review->user->member_name }}</div>
                </div>
                <div class="item__content--comment">{{ $review->comment }}</div>
            </div>
            @endforeach
        </div>

        {{-- 💡 3. コメント入力エリア（ログインユーザーのみに表示） --}}
        {{-- 💡 【修正】コメント入力エリア（ログインかつ未売却時のみ表示） --}}
        @auth
            <div>商品へのコメント</div>
            <form action="{{ route('comments.store', $item) }}" class="item__content--comment-form" method="POST" novalidate>
                @csrf
                <textarea name="comment" class="common__input-textbox" required placeholder="コメントを入力してください"></textarea>
                @error('comment')
                    <div style="color: red;">{{ $message }}</div>
                @enderror
                <button type="submit" class="common__button">コメントを送信する</button>
            </form>
        @else
            @if($item->sales_status != 3)
                <a href="{{ route('login', ['url' => url()->current()]) }}" class="common__button--uncertified">
                    ログインしてコメントする
                </a>
            @endif
        @endauth
    </div>
</div>

{{-- コメント投稿時・商品購入画面で購入済み商品を表示しようとした時に表示するモーダル --}}
@if(session('modal_message'))
<div class="modal-wrapper">
    <input type="checkbox" id="modal-trigger" checked style="display: none;">
    <div class="modal-overlay">
        <div class="modal-content">
            <p class="modal-text">{{ session('modal_message') }}</p>
            <label for="modal-trigger" class="common__button modal-close-btn">閉じる</label>
        </div>
    </div>
</div>
@endif

@endsection