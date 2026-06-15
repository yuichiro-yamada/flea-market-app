@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/mypage.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
@auth
    <div class="mypage__form">
        <div class="mypage__picture">
            <img src="{{ $user->member_image && file_exists(public_path('storage/images/profile/' . $user->member_image)) ? '/storage/images/profile/' . $user->member_image : '/images/profile/silver.png' }}" class="common__picture--photo">

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
        <!-- 出品した商品ボタン -->
        <a href="{{ url('/mypage?page=sell') }}" 
           class="{{ $page === 'sell' ? 'index__menu--link-red' : 'index__menu--link' }}">
            出品した商品
        </a>
        
        <!-- 購入した商品ボタン -->
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
                {{-- 💡 親のdivに position: relative と overflow: hidden を直接インラインで強制指定します --}}
                <div class="index__items--picture" style="position: relative !important; overflow: hidden !important; display: inline-block; width: 100%;">
                    <img src="{{ asset('storage/images/items/' . $item->item_image) }}" style="width: 100%; display: block;">
                    
                    {{-- 💡 クラス名は元々の設計通り「sold-badge」にします --}}
                    @if($item->sales_status == 3)
                        <div class="sold-badge"><span>SOLD</span></div>
                    @endif
                </div>
                <div class="index__items--name">
                    {{ $item->item_name }}
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>

@endsection