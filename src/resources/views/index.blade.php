@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
    <div class="index__menu">
        <div class="index__menu--wrapper">
            {{-- 現在のURLに tab=mylist が「ない」ときは、おすすめを赤文字（アクティブ）にする --}}
            @if(request()->query('tab') !== 'mylist')
                <div class="index__menu--link-red">おすすめ</div>
            @else
                <a href="/" class="index__menu--link">おすすめ</a>
            @endif

            {{-- 💡 現在のURLに tab=mylist が「ある」ときは、マイリストを赤文字（アクティブ）にする --}}
            @if(request()->query('tab') === 'mylist')
                <div class="index__menu--link-red">マイリスト</div>
            @else
                <a href="/?tab=mylist" class="index__menu--link">マイリスト</a>
            @endif
        </div>
    </div>
    
    <div class="index__items--wrapper">
        <div class="index__items--row">
            {{-- 💡 コントローラ側で中身が切り替わっているため、ループ処理はこのままでOKです --}}
            @foreach($items as $item)
            <div class="index__items--box">
                {{-- 💡 画像を囲むdivに、詳細画面と同じクラス「item__picture-container」を追加します --}}
                <div class="index__items--picture item__picture-container">
                    <img src="storage/images/items/{{ $item->item_image }}">
                    
                    {{-- 💡 sales_status が 3（売却済み）の場合にSOLDリボンを表示 --}}
                    @if($item->sales_status == 3)
                        <div class="sold-badge"><span>SOLD</span></div>
                    @endif
                </div>
                <div class="index__items--name">
                    <a href="{{ route('items.show', ['item' => $item->id]) }}">
                        {{ $item->item_name }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection
