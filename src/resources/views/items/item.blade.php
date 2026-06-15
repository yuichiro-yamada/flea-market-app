@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/item.css') }}" />
@endsection

@section('content')
<div class="item__form">
    <div class="item__picture-container" style="position: relative; display: inline-block; overflow: hidden;">
        <img src="/storage/images/items/{{ $item->item_image }}" class="item__picture--photo">
        @if($item->sales_status == 3)
            <div class="sold-badge"><span>SOLD</span></div>
        @endif
    </div>
    <div class="item__content">
        <h1>{{ $item->item_name }}</h1>
        <div class="item__content--brand">{{ $item->brand_name }}</div>
        <div class="item__content--price">
            <div class="item__content--price-unit">¥</div>
            <div class="item__content--price-tag">{{ $item->item_price_formatted }}</div>
            <div class="item__content--price-tax">(税込)</div>
        </div>
        <div class="item__content--mark">
            <div class="item__content--mark-unit">
                @auth
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
                @else
                    {{-- 💡 ログインしていない場合：画像を表示するだけでクリック不可 --}}
                    <img src="/images/heart_default.png" alt="いいね">
                @endauth

                {{-- いいねの総数を表示（loadCountを使うことで _count で取得できます） --}}
                <div class="item__content--mark-count">
                    {{ $item->favoritedByUsers_count ?? $item->favoritedByUsers->count() }}
                </div>
            </div>
            <div class="item__content--mark-unit">
                <img src="/images/baloon.png">
                <div class="item__content--mark-count">
                    {{ $item->reviews_count ?? $item->reviews->count() }}
                </div>
            </div>
        </div>
        @if($item->sales_status == 3)
            <button class="common__button" disabled style="background-color: #cccccc; cursor: not-allowed;">売り切れました</button>
        @else
            <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="common__button" style="text-align: center; text-decoration: none; display: block;">購入手続きへ</a>
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
        <h2>コメント ({{ $item->reviews_count }})</h2>

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
            @if($item->sales_status == 3)
                {{-- 💡 売り切れ時はコメント入力フォーム自体を非表示にします --}}
            @else
                <div>商品へのコメント</div>
                <form action="{{ route('comments.store', $item) }}" class="item__content--comment-form" method="POST">
                    @csrf
                    @error('comment')
                        <div style="color: red;">{{ $message }}</div>
                    @enderror
                    <textarea name="comment" class="common__input-textbox" required placeholder="コメントを入力してください"></textarea>
                    <button type="submit" class="common__button">コメントを送信する</button>
                </form>
            @endif
        @else
            @if($item->sales_status != 3)
                <p style="color: #666; margin-top: 20px;">※コメントを投稿するには<a href="{{ route('login') }}">ログイン</a>が必要です。</p>
            @endif
        @endauth
    </div>
</div>

{{-- 💡 【追加】購入完了時のみ表示されるHTML＆CSSモーダル（JS完全不要） --}}
@if(session('purchase_completed'))
<div class="modal-wrapper">
    <input type="checkbox" id="modal-trigger" checked style="display: none;">
    <div class="modal-overlay">
        <div class="modal-content">
            <p class="modal-text">購入が完了しました</p>
            <label for="modal-trigger" class="common__button modal-close-btn">閉じる</label>
        </div>
    </div>
</div>
@endif
@endsection