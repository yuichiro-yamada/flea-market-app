@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/mypage.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
@auth
    <div class="mypage__form">
        <div class="mypage__picture">
            <img src="{{ asset('storage/images/profile/' . $user->member_image) }}" class="common__picture--photo">

            <div class="mypage__name">{{ $user->member_name }}</div>
            <a href="mypage/profile" class="mypage__picture--select">プロフィールを編集</a>
        </div>
    </div>
@endauth
@php
    // クエリパラメータ 'page' を取得。未指定の場合はデフォルトで 'sell'（出品した商品）とする
    $page = request()->query('page', 'sell');
@endphp

<div class="index__menu">
    <div class="index__menu--wrapper">
        {{-- 表示中のページ（sell）に応じてアクティブクラスを動的に切り替え --}}
        <a href="{{ url('/mypage?page=sell') }}" 
           class="{{ $page === 'sell' ? 'index__menu--link-red' : 'index__menu--link' }}">
            出品した商品
        </a>
        
        {{-- 表示中のページ（buy）に応じてアクティブクラスを動的に切り替え --}}
        <a href="{{ url('/mypage?page=buy') }}" 
           class="{{ $page === 'buy' ? 'index__menu--link-red' : 'index__menu--link' }}">
            購入した商品
        </a>
    </div>
</div>

<div class="index__items--wrapper">
    <div class="index__items--row">
        @if($items->isEmpty())
            <p>商品はありません。</p>
        @else
            @foreach($items as $item)
            <div class="index__items--box">
                {{-- 親のdivに position: relative と overflow: hidden を直接インラインで強制指定 --}}
                <a href="{{ route('items.show', ['item' => $item->id]) }}">
                    <div class="index__items--picture common__badge--position">
                        <img src="{{ asset('storage/images/items/' . $item->item_image) }}" style="width: 100%; display: block;">
                        
                        {{-- ユーザーの関与度（当事者か第三者か）とステータスに応じてバッジを分岐 --}}
                        {{-- UNSOLD：取引中(2) かつ 自身が出品者または購入者の場合 --}}
                        @if ($item->sales_status == 2 && ($item->seller_id == Auth::id() || $item->buyer_id == Auth::id()))
                            <div class="unsold-badge"><span>未入金</span></div>
                        {{-- SOLD：売却済(3) または 取引中(2)だが自身が第三者の場合 --}}
                        @elseif ($item->sales_status == 3 || ($item->sales_status == 2 && $item->seller_id != Auth::id() && $item->buyer_id != Auth::id()))
                            <div class="sold-badge"><span>SOLD</span></div>
                        @endif
                    </div>
                </a>
                <div class="index__items--name">
                    <a href="{{ route('items.show', ['item' => $item->id]) }}">
                        {{ $item->item_name }}
                    </a>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>

{{-- セッションメッセージがある場合のみ、CSS制御用の隠しチェックボックスを用いたモーダルを表示 --}}
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
