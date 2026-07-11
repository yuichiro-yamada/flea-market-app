@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
    <div class="index__menu">
        <div class="index__menu--wrapper">
            {{-- 現在のURLに tab=mylist がないときは、おすすめをアクティブにする --}}
            @if(request()->query('tab') !== 'mylist')
                <div class="index__menu--link-red">おすすめ</div>
            @else
                <a href="/?{{ http_build_query(array_merge(request()->query(), ['tab' => null])) }}" class="index__menu--link">おすすめ</a>
            @endif

            {{-- 現在のURLに tab=mylist があるときは、マイリストをアクティブにする --}}
            @if(request()->query('tab') === 'mylist')
                <div class="index__menu--link-red">マイリスト</div>
            @else
                <a href="/?{{ http_build_query(array_merge(request()->query(), ['tab' => 'mylist'])) }}" class="index__menu--link">マイリスト</a>
            @endif
        </div>
    </div>
    <div class="index__items--wrapper">
        @if(request()->filled('keyword'))
            <div class="index__search-keyword">
                検索キーワード：{{ request()->query('keyword') }}
            </div>
        @endif
        <div class="index__items--row">
            {{-- おすすめ・マイリストともに同じレイアウトで一覧表示 --}}
            @foreach($items as $item)
            <div class="index__items--box">
                {{-- 商品画像と販売ステータスバッジ --}}
                <a href="{{ route('items.show', ['item' => $item->id]) }}">
                    <div class="index__items--picture common__badge--position">
                        <img src="storage/images/items/{{ $item->item_image }}">
                        @if ($item->sales_status == 2 && ($item->seller_id == Auth::id() || $item->buyer_id == Auth::id()))
                            <div class="unsold-badge"><span>未入金</span></div>
                        @elseif ($item->sales_status == 3 || ($item->sales_status == 2 && $item->seller_id != Auth::id() && $item->buyer_id != Auth::id()))
                            <div class="sold-badge"><span>SOLD</span></div>
                        @endif
                    </div>
                </a>
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
