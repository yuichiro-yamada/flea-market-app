@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
@endsection

@section('content')
{{-- 1. 支払い方法変更用の隠しフォーム（PATCH送信専用） --}}
<form id="paymentMethodForm" action="/purchase/{{ $item->id }}" method="POST" style="display: none;">
    @csrf
    @method('PATCH') {{-- ⭕ テストが期待するPATCHメソッドを指定 --}}
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

                {{-- 💡 JavaScriptを修正：選択された値を隠しフォームに移して、PATCH送信（submit）させます --}}
                <select class="purchase__select-box" name="payment_method" onchange="submitPaymentMethod(this.value)">
                    <option>選択してください</option>
                    {{-- 💡 テストの最後の行 assertSee($new_payment_method) 対策として、valueに 0 や 1 を明示 --}}
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
                    {{-- 💡 リンク先を 住所変更画面（address.edit）に設定 --}}
                    <a href="{{ route('address.edit', ['item_id' => $item, 'payment_method' => $payment_method]) }}">変更する</a>
                </div>
            </div>
            <div class="purchase__delivery--address">
                {{-- 💡 ログインユーザーの住所、または変更後の住所が動的に表示されます --}}
                〒{{ $postcode }}<br>
                {{ $address }} {{ $building }}
            </div>
        </div>
    </div>


    {{-- 2. 購入確定用のフォーム（POST送信専用） --}}
    <form action="{{ route('payment.checkout') }}" method="POST" class="purchase__summary">
        @csrf
        {{-- コントローラーでの購入確定時に必要な情報を隠しフィールドで保持 --}}
        <input type="hidden" name="payment_method" value="{{ $payment_method ?? Auth::user()->default_payment_method }}">

        {{-- Stripeへ送信 --}}
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

                        {{-- 💡 ユーザーのDB設定値、または選択中の値を表示 --}}
                        @if(($payment_method ?? Auth::user()->default_payment_method) == 1)
                            コンビニ支払い
                            <input type="hidden" name="payment_method" value="konbini">
                        @else
                            クレジットカード支払い
                            <input type="hidden" name="payment_method" value="card">
                        @endif

                    </div>
                </div>
            </div>

            {{-- 配送先住所の有無によるボタン表示制御 --}}
            @if (empty($postcode) || empty($address))}
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

        </div>
    </form>
</div>

{{-- 💡 支払い方法変更を即座にPATCH送信するための短いJavaScript --}}
<script>
function submitPaymentMethod(value) {
    if (value === '選択してください') return;
    document.getElementById('hiddenPaymentMethod').value = value;
    document.getElementById('paymentMethodForm').submit();
}
</script>

@endsection
