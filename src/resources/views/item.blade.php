@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/item.css') }}" />
@endsection

@section('content')
<div class="item__form">
    <img src="/images/items/Living+Room+Laptop.jpg" class="item__picture--photo">
    <div class="item__content">
        <h1>Living Room Laptop</h1>
        <div class="item__content--brand">ブランド名</div>
        <div class="item__content--price">
            <div class="item__content--price-unit">¥</div>
            <div class="item__content--price-tag">47,000</div>
            <div class="item__content--price-tax">(税込)</div>
        </div>
        <div class="item__content--mark">
            <div class="item__content--mark-unit">
                <img src="/images/heart_default.png">
                <div class="item__content--mark-count">3</div>
            </div>
            <div class="item__content--mark-unit">
                <img src="/images/baloon.png">
                <div class="item__content--mark-count">3</div>
            </div>
        </div>
        <button class="common__button">購入手続きへ</button>
        <h2>商品説明</h2>
        <div>コメントが入る</div>
        <h2>商品の情報</h2>
        <div class="item__info--wrapper">
            <div class="item__info--title">カテゴリ</div>
            <div class="item__category--box">
                <div class="item__category--select">ファッション</div>
                <div class="item__category--select">家電</div>
                <div class="item__category--select">インテリア</div>
                <div class="item__category--select">レディース</div>
                <div class="item__category--select">メンズ</div>
                <div class="item__category--select">コスメ</div>
                <div class="item__category--select">本</div>
                <div class="item__category--select">ゲーム</div>
                <div class="item__category--select">スポーツ</div>
                <div class="item__category--select">キッチン</div>
            </div>
        </div>
        <div class="item__info--wrapper">
            <div class="item__info--title">商品の状態</div>
            <div class="item__info--content">良好</div>
        </div>
        <h2>コメント(1)</h2>
        <div class="item__content--picture">
            <img src="/images/profile/azu.jpg" class="item__content--picture-photo">
            <div class="item__content--picture-name">山田あづき</div>
        </div>
        <div class="item__content--comment">こちらにコメントが入ります。</div>
        <div>商品へのコメント</div>
        <textarea class="common__input-textbox"></textarea>
        <button class="common__button">コメントを送信する</button>
@endsection