@extends('layouts.app')

@section('content')
<div class="common__form">
    <h1>ログイン</h1>
    <form action="{{ route('login') }}" method="post" novalidate>
        @csrf
        <div class="common__heading">メールアドレス</div>
        <input type="text"  class="common__input-box" name="email" value="{{old('email')}}">
        <div class="error-message">
            @error('email')
            {{$message}}
            @enderror
        </div>
        <div class="common__heading">パスワード</div>
        <input type="text"  class="common__input-box" name="password">
        <div class="error-message">
            @error('password')
            {{$message}}
            @enderror
        </div>
        <button class="common__button" type="submit">ログイン</button>
        <div class=common__button--below>
            <a href="/register">会員登録はこちら</a>
        </div>
    </form>
</div>
@endsection