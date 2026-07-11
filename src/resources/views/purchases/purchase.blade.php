@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
@endsection

@section('content')
{{-- 支払い方法変更用の隠しフォーム（PATCH送信専用） --}}
<form id="paymentMethodForm" action="/purchase/{{ $item->id }}" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="payment_method" id="hiddenPaymentMethod">
</form>

<div class="purchase__form">
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

                {{-- 支払い方法を変更したら、JavaScript経由でPATCHリクエストを送信 --}}
                <select class="purchase__select-box" name="payment_method" onchange="submitPaymentMethod(this.value)">
                    <option>選択してください</option>
                    {{-- DBで管理している支払い方法の値（0: クレジットカード、1: コンビニ） --}}
                    <option value="0" {{ $payment_method === '0' || (Auth::user()->default_payment_method === 0 && is_null($payment_method)) ? 'selected' : '' }}>クレジットカード支払い</option>
                    <option value="1" {{ $payment_method === '1' || (Auth::user()->default_payment_method === 1 && is_null($payment_method)) ? 'selected' : '' }}>コンビニ支払い</option>
                </select>

            </div>
        </div>

        {{-- 配送先部分の修正 --}}
        <div class="purchase__delivery">
            <div class="purchase__delivery--wrap">
                <div class="purchase__delivery--title">配送先</div>
                <div>
                    {{-- 配送先住所の変更画面へ遷移 --}}
                    <a href="{{ route('address.edit', ['item_id' => $item, 'payment_method' => $payment_method]) }}">変更する</a>
                </div>
            </div>
            <div class="purchase__delivery--address">
                {{-- 現在の配送先住所を表示（変更後の住所を優先） --}}
                〒{{ $postcode }}<br>
                {{ $address }} {{ $building }}
            </div>
        </div>
    </div>


    {{-- 購入確定時にStripe決済へ遷移するPOSTフォーム --}}
    <form action="{{ route('payment.checkout') }}" method="POST" class="purchase__summary">
        @csrf
        {{-- 購入処理に必要な支払い方法を保持 --}}
        <input type="hidden" 
            id="checkoutPaymentMethod" 
            name="payment_method" 
            value="{{ $payment_method ?? Auth::user()->default_payment_method }}">

        {{-- Stripe決済に必要な商品情報 --}}
        <input type="hidden" name="product_name" value="{{ $item->item_name }}">
        <input type="hidden" name="price" value="{{ $item->item_price }}">
        <input type="hidden" name="item_id" value="{{ $item->id }}">

        <div class="purchase__summary">
            <div class="purchase__summary--wrap">
                <div class="purchase__summary--item">
                    <div class="purchase__summary--title">商品代金</div>
                    <div class="purchase__summary--content">¥{{ $item->item_price_formatted }}</div>
                </div>
                <div class="purchase__summary--item">
                    <div class="purchase__summary--title">支払い方法</div>
                    <div class="purchase__summary--content">

                        {{-- 現在選択されている支払い方法を表示 --}}
                        @if(($payment_method ?? Auth::user()->default_payment_method) == 1)
                            コンビニ支払い
                        @else
                            クレジットカード支払い
                        @endif

                    </div>
                </div>
            </div>

            {{-- 配送先住所の有無によるボタン表示制御 --}}
            @if ($item->seller_id == Auth::user()->id)
                <button type="button" class="common__button--disabled" disabled>購入する</button>
                <div class="error-message">
                    出品者ご本人のためご注文できません
                </div>
            @else
                @if (empty($postcode) || empty($address))
                    {{-- 配送先住所ない場合は非活性 --}}
                    <button type="button" class="common__button--disabled" disabled>購入する</button>
                    <div class="error-message">
                        配送先住所を設定してください
                    </div>
                @else
                    {{-- 配送先住所ある場合は活性 --}}
                    <button type="submit" class="common__button">
                        購入する
                    </button>
                @endif
            @endif
        </div>
    </form>
</div>

{{-- 支払い方法変更を即座にPATCH送信するための短いJavaScript --}}
<script>
function submitPaymentMethod(value) {
    if (value === '選択してください') return;

    // PATCH送信用
    document.getElementById('hiddenPaymentMethod').value = value;

    // 購入フォーム用
    document.getElementById('checkoutPaymentMethod').value = value;

    // DB更新
    document.getElementById('paymentMethodForm').submit();
}
</script>

@endsection
