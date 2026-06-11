@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<div class="common__form">
    <h1>プロフィール設定</h1>
    <form action="/mypage/profile" method="post" novalidate>
        @csrf
        @method('PATCH')
        <div class="profile__picture">
            <img src="/images/profile/azu.jpg" class="common__picture--photo">
            <div class="profile__picture--select">画像を選択する</div>
        </div>
        <div class="common__">ユーザー名</div>
        <input type="text"  class="common__input-box" name="member_name" value="{{ $user->member_name }}">
        <div>郵便番号</div>
        <input type="text"  class="common__input-box" name="postcode" value="{{ $user->postcode }}">
        <div>住所</div>
        <input type="text"  class="common__input-box" name="address" value="{{ $user->address }}">
        <div>建物名</div>
        <input type="text"  class="common__input-box" name="building" value="{{ $user->building }}">
        <button class="common__button" type="submit">更新する</button>
    </form>
</div>
@endsection