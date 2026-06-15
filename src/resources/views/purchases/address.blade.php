@extends('layouts.app')

@section('content')
<div class="common__form">
    <h1>住所の変更</h1>
    
    {{-- 💡 ルーティングと繋ぐためのformタグを追加 --}}
    <form action="{{ route('address.update', $item) }}" method="POST">
        @csrf
        
        <div>郵便番号</div>
        {{-- 💡 name属性をコントローラー側と合わせ、value属性で初期値を表示します --}}
        <input type="text" class="common__input-box" name="postcode" value="{{ session('shipping_postcode', Auth::user()->postcode) }}" required>
        
        <div>住所</div>
        <input type="text" class="common__input-box" name="address" value="{{ session('shipping_address', Auth::user()->address) }}" required>
        
        <div>建物名</div>
        <input type="text" class="common__input-box" name="building" value="{{ session('shipping_building', Auth::user()->building) }}">
        
        {{-- 💡 フォーム送信（submit）ができるようにボタンを調整 --}}
        <button type="submit" class="common__button">更新する</button>
    </form>
</div>
@endsection
