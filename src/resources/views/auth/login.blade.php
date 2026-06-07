@extends('layouts.app')

@section('content')
<div class="common__form">
    <h1>ログイン</h1>
    <div class="common__">メールアドレス</div>
    <input type="text"  class="common__input-box" name="email">
    <div>パスワード</div>
    <input type="text"  class="common__input-box" name="password">
    <button class="common__button">ログイン</button>
    <div class=common__button--below>
        <a href="*********">会員登録はこちら</a>
    </div>
</div>
@endsection