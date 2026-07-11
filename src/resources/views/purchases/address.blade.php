@extends('layouts.app')

@section('content')
<div class="common__form">
    <h1>住所の変更</h1>
    <form action="{{ route('address.update', $item_id) }}" method="POST"  novalidate>
        @csrf
        
        <div>郵便番号</div>
        <input type="text" class="common__input-box" name="shipping_postcode" 
        value="{{ Auth::user()->shipping_postcode ?? Auth::user()->postcode }}" required>
        <div class="error-message">
            @error('shipping_postcode')
            {{$message}}
            @enderror
        </div>
        
        <div>住所</div>
        <input type="text" class="common__input-box" name="shipping_address" 
        value="{{ Auth::user()->shipping_address ?? Auth::user()->address }}" required>
        <div class="error-message">
            @error('shipping_address')
            {{$message}}
            @enderror
        </div>
        
        <div>建物名</div>
        <input type="text" class="common__input-box" name="shipping_building" 
        value="{{ Auth::user()->shipping_postcode ? Auth::user()->shipping_building : Auth::user()->building }}">
        <div class="error-message">
            @error('shipping_building')
            {{$message}}
            @enderror
        </div>

        <button type="submit" class="common__button">更新する</button>
    </form>
</div>
@endsection
