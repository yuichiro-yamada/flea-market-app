@extends('layouts.app')

@section('content')
<div class="common__form">
    登録していただいたメールアドレスに認証メールを送信しました。<br>
    メール認証を完了してください。
        <button class="common__button" type="submit">ログイン</button>
        <div class=common__button--below>
            <a href="/register">会員登録はこちら</a>
        </div>
</div>
@endsection