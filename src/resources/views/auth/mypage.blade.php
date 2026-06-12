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
<div class="index__menu">
    <div class="index__menu--wrapper">
        <div class="index__menu--link">出品した商品</div>
        <div class="index__menu--link">購入した商品</div>
    </div>
</div>
<div class="index__items--wrapper">
    <div class="index__items--row">

        @foreach($items as $item)
        <div class="index__items--box">
            <div class="index__items--picture">
                <img src="images/items/{{ $item->item_image }}">
            </div>
            <div class="index__items--name">
                {{ $item->item_name }}
            </div>
        </div>
        @endforeach

    </div>
</div>
@endsection