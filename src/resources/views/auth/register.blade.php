@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
@endsection

@section('content')
<div class="common__form">
    <h1>会員登録</h1>
    <div class="common__">ユーザー名</div>
    <input type="text"  class="common__input-box" name="user_name">
    <div>メールアドレス</div>
    <input type="text"  class="common__input-box" name="email">
    <div>パスワード</div>
    <input type="text"  class="common__input-box" name="password">
    <div>確認用パスワード</div>
    <input type="text"  class="common__input-box" name="confirmation-password">
    <button class="common__button">会員登録</button>
    <div class=common__button--below>
        <a href="*********">ログインはこちら</a>
    </div>
</div>
@endsection