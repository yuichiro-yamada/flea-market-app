@extends('layouts.auth')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/verify.css') }}" />
@endsection

@section('content')
<div class="common__form">
    <p>登録していただいたメールアドレスに認証メールを送信しました。</p>
    <p>メール認証を完了してください。</p>
    <a href="https://mailtrap.io" target="_blank" class="verify__button">認証はこちらから</a>
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <!-- 👇 buttonタグの見た目をCSSで完全に「リンク」に変え去ります -->
        <button type="submit" class="verify__resend-email">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection
