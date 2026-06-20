@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
@endsection

@section('content')
{{-- 💡 全体を1つのformタグで囲み、最後にまとめて「購入する」ボタンでPOST送信できるようにします --}}
<form action="{{ route('purchase.store', $item) }}" method="POST" class="purchase__form" id="purchaseForm">
    @csrf

    <div class="purchase__form--main">
        <div class="purchase__content">
            <img src="/storage/images/items/{{ $item->item_image }}" class="purchase__picture--photo">
            <div class="purchase__content--info">
                <h1>{{ $item->item_name }}</h1>
                <div class="purchase__content--price">
                    <div class="purchase__content--price-unit">¥</div>
                    <div class="purchase__content--price-tag">{{ $item->item_price_formatted }}</div>
                </div>
            </div>
        </div>
        <div class="purchase__payment">
            <div class="purchase__payment--title">支払い方法</div>
            <div class="purchase__select-box--wrapper">
                {{-- 💡 1行だけJS（onchange）を追加。これにより切り替えた瞬間にページが自動リロード（GET送信）されます --}}
                <select class="purchase__select-box" name="payment_method" onchange="this.form.method='GET'; this.form.action=''; this.form.submit();">
                    {{-- URLパラメーター（request）の状態を見て、選択状態（selected）を維持します --}}
                    <option value="コンビニ支払い" hidden {{ request('payment_method') ? '' : 'selected' }}>選択してください</option>
                    <option value="クレジットカード支払い" {{ request('payment_method') === 'クレジットカード支払い' ? 'selected' : '' }}>クレジットカード支払い</option>
                    <option value="コンビニ支払い" {{ request('payment_method') === 'コンビニ支払い' ? 'selected' : '' }}>コンビニ支払い</option>
                </select>
            </div>
        </div>

        {{-- 配送先部分の修正 --}}
        <div class="purchase__delivery">
            <div class="purchase__delivery--wrap">
                <div class="purchase__delivery--title">配送先</div>
                <div class="purchase__delivery--edit">
                    {{-- 💡 リンク先を 住所変更画面（address.edit）に設定 --}}
                    <a href="{{ route('address.edit', $item) }}">変更する</a>
                </div>
            </div>
            <div class="purchase__delivery--address">
                {{-- 💡 ログインユーザーの住所、または変更後の住所が動的に表示されます --}}
                〒{{ $postcode }}<br>
                {{ $address }} {{ $building }}
            </div>
        </div>
        </div>
    </div>
    <div class="purchase__summary">
        <div class="purchase__summary--wrap">
            <div class="purchase__summary--item">
                <div class="purchase__summary--title">商品代金</div>
                <div class="purchase__summary--content">¥{{ $item->item_price_formatted }}</div>
            </div>
            <div class="purchase__summary--item">
                <div class="purchase__summary--title">支払い方法</div>
                {{-- 💡 URLに支払い方法が残っていればそれを表示し、なければ初期値の「コンビニ支払い」を表示します --}}
                <div class="purchase__summary--content">{{ request('payment_method', 'コンビニ支払い') }}</div>
            </div>
        </div>
        {{-- 💡 「購入する」ボタンを submit タイプに変更。クリックすると本来のPOST処理に飛びます --}}
        <button type="submit" class="common__button" style="width: 100%; border: none; cursor: pointer;">購入する</button>
    </div>
</form>

@endsection
