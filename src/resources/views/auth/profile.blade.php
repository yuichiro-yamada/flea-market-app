@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<div class="common__form">
    <h1>プロフィール設定</h1>
    <div class="profile__picture">
        <img src="/images/profile/azu.jpg" class="common__picture--photo">
        <div class="profile__picture--select">画像を選択する</div>
    </div>
    <div class="common__">ユーザー名</div>
    <input type="text"  class="common__input-box" name="user_name">
    <div>郵便番号</div>
    <input type="text"  class="common__input-box" name="post-code">
    <div>住所</div>
    <input type="text"  class="common__input-box" name="address">
    <div>建物名</div>
    <input type="text"  class="common__input-box" name="building">
    <button class="common__button">更新する</button>
</div>
@endsection