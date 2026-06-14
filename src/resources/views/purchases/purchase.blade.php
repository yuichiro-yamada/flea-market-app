@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
@endsection

@section('content')
<div class="purchase__form">
    <div class="purchase__form--main">
        <div class="purchase__content">
            <img src="/images/items/Waitress+with+Coffee+Grinder.jpg" class="purchase__picture--photo">
            <div class="purchase__content--info">
                <h1>Living Room Laptop</h1>
                <div class="purchase__content--price">
                    <div class="purchase__content--price-unit">¥</div>
                    <div class="purchase__content--price-tag">47,000</div>
                </div>
            </div>
        </div>
        <div class="purchase__payment">
            <div class="purchase__payment--title">支払い方法</div>
            <div class="purchase__select-box--wrapper">
                <select  class="purchase__select-box" name="password">
                    <option>選択してください</option>
                    <option>クレジットカード支払い</option>
                    <option>コンビニ支払い</option>
                </select>
            </div>
        </div>
        <div class="purchase__delivery">
            <div class="purchase__delivery--wrap">
                <div class="purchase__delivery--title">配送先</div>
                <div class="purchase__delivery--edit">
                    <a href="xxx.htm">変更する</a>
                </div>
            </div>
            <div class="purchase__delivery--address">
                〒XXX-YYYY<br>
                ここは住所と建物が入ります
            </div>
        </div>
    </div>
    <div class="purchase__summary">
        <div class="purchase__summary--wrap">
            <div class="purchase__summary--item">
                <div class="purchase__summary--title">商品代金</div>
                <div class="purchase__summary--content">¥47,000</div>
            </div>
            <div class="purchase__summary--item">
                <div class="purchase__summary--title">支払い方法</div>
                <div class="purchase__summary--content">コンビニ支払い</div>
            </div>
        </div>
        <div class="common__button">購入する</div>
    </div>
</div>
@endsection