@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
    <div class="index__menu">
        <div class="index__menu--wrapper">
            <div class="index__menu--link">おすすめ</div>
            <div class="index__menu--link">マイリスト</div>
        </div>
    </div>
    <div class="index__items--wrapper">
        <div class="index__items--row">
            @foreach($items as $item)
            <div class="index__items--box">
                <div class="index__items--picture">
                    <img src="images/items/{{ $item->item_image }}">
                </div>
                <div class="index__items--name">
                    {{ $item->item_name }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection