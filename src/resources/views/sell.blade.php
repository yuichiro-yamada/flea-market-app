@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/sell.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<div class="common__form">
    <h1>商品の出品</h1>
    <div>ユーザー名</div>
    <div  class="sell__photo--box">
        <div class="profile__picture--select">画像を選択する</div>
    </div>
    <div class="sell__section">商品の詳細</div>
    <div>カテゴリ</div>
    <div class="sell__category--box">
        <div class="sell__category--select">ファッション</div>
        <div class="sell__category--select">家電</div>
        <div class="sell__category--select">インテリア</div>
        <div class="sell__category--select">レディース</div>
        <div class="sell__category--select">メンズ</div>
        <div class="sell__category--select">コスメ</div>
        <div class="sell__category--select">本</div>
        <div class="sell__category--select">ゲーム</div>
        <div class="sell__category--select">スポーツ</div>
        <div class="sell__category--select">キッチン</div>
    </div>
    <div>商品の状態</div>
    <div class="sell__select-box--wrapper">
        <select  class="sell__select-box" name="password">
            <option>良好</option>
            <option>目立った傷や汚れなし</option>
            <option>やや傷や汚れあり</option>
            <option>状態が悪い</option>
        </select>
    </div>
    <div class="sell__section">商品と説明</div>
    <div>商品名</div>
    <input type="text"  class="common__input-box" name="confirmation-password">
    <div>ブランド名</div>
    <input type="text"  class="common__input-box" name="confirmation-password">
    <div>商品の説明</div>
    <textarea  class="common__input-textbox" name="confirmation-password"></textarea>
    <div>販売価格</div>
    <div class="sell__input-box--wrapper">
        <input type="text"  class="common__input-box" name="confirmation-password">
    </div>

    <button class="common__button">出品する</button>
    <div class=common__button--below>
        <a href="*********">ログインはこちら</a>
    </div>
</div>
@endsection