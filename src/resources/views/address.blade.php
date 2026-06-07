@extends('layouts.app')

@section('content')
<div class="common__form">
    <h1>住所の変更</h1>
    <div>郵便番号</div>
    <input type="text"  class="common__input-box" name="post-code">
    <div>住所</div>
    <input type="text"  class="common__input-box" name="address">
    <div>建物名</div>
    <input type="text"  class="common__input-box" name="building">
    <button class="common__button">更新する</button>
</div>
@endsection