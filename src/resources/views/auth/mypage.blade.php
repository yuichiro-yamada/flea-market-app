@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/mypage.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
<div class="mypage__form">
    <div class="mypage__picture">
        <img src="/images/profile/azu.jpg" class="common__picture--photo">
        <div class="mypage__name">山田あづき</div>
        <div class="mypage__picture--select">プロフィールを編集</div>
    </div>
</div>
<div class="index__menu">
    <div class="index__menu--wrapper">
        <div class="index__menu--link">おすすめ</div>
        <div class="index__menu--link">マイリスト</div>
    </div>
</div>
<div class="index__items--wrapper">
    <div class="index__items--row">

        <div class="index__items--box">
            <div class="index__items--picture">
                <img src="images/items/Armani+Mens+Clock.jpg">
            </div>
            <div class="index__items--name">
                商品名
            </div>
        </div>

        <div class="index__items--box">
            <div class="index__items--picture">
                <img src="images/items/Armani+Mens+Clock.jpg">
            </div>
            <div class="index__items--name">
                商品名
            </div>
        </div>

        <div class="index__items--box">
            <div class="index__items--picture">
                <img src="images/items/Armani+Mens+Clock.jpg">
            </div>
            <div class="index__items--name">
                商品名
            </div>
        </div>

        <div class="index__items--box">
            <div class="index__items--picture">
                <img src="images/items/Armani+Mens+Clock.jpg">
            </div>
            <div class="index__items--name">
                商品名
            </div>
        </div>

    </div>
</div>
@endsection