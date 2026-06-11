@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
@endsection

@section('content')
<div class="common__form">
    <h1>会員登録</h1>
    <form action="register" method="post" novalidate>
        @csrf
        <div class="common__heading">ユーザー名</div>
        <input type="text"  class="common__input-box" name="member_name" value="{{old('member_name')}}">
        <div class="error-message">
            @error('member_name')
            {{$message}}
            @enderror
        </div>
        <div class="common__heading">メールアドレス</div>
        <input type="text"  class="common__input-box" name="email" value="{{old('email')}}">
        <div class="error-message">
            @error('email')
            {{$message}}
            @enderror
        </div>
        <div class="common__heading">パスワード</div>
        <input type="text"  class="common__input-box" name="password">
        <div class="common__heading">確認用パスワード</div>
        <input type="text"  class="common__input-box" name="password_confirmation">
        <div class="error-message">
            @error('password')
            {{$message}}
            @enderror
        </div>
        <button class="common__button" type="submit">会員登録</button>
    </form>
    <div class=common__button--below>
        <a href="/login">ログインはこちら</a>
    </div>
</div>
@endsection