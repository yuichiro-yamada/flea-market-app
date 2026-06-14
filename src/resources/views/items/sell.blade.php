@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/sell.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- ★formタグのすぐ内側に配置（タグの閉じ順を整えました） -->
    <div class="common__form">
        <h1>商品の出品</h1>
        <div>ユーザー名</div>

        <!-- 点線の枠エリア（画像が選択されたら自動で縦に伸びます） -->
        <div class="sell__photo--box">
            <div class="sell__photo--inner-container">
                
                <!-- 画像を選択するボタン（常に上部に表示） -->
                <label style="cursor: pointer;">
                    <div class="profile__picture--select">画像を選択する</div>
                    
                    <input type="file" name="item_image" accept="image/*" style="display: none;" 
                        onchange="document.getElementById('form-action').value='select_image'; this.form.submit();">
                </label>

                <!-- ★枠の内側、ボタンの下に左右中央で表示する -->
                @if(session('item_tmp_image_path'))
                    <div class="sell__photo--preview-area">
                        <!-- ファイル名表示 -->
                        <div class="file-info-display">
                            選択中のファイル: <strong>{{ session('item_tmp_image_name') }}</strong>
                        </div>

                        <!-- プレビュー画像表示 -->
                        <div class="preview-image-container">
                            <img src="/storage/{{ session('item_tmp_image_path') }}" class="common__picture--photo">
                        </div>

                        <!-- フォーム送信用の隠し入力 -->
                        <input type="hidden" name="item_tmp_image_path" value="{{ session('item_tmp_image_path') }}">
                    </div>
                @endif

            </div>
        </div>

        <div class="sell__section">商品の詳細</div>
        <div>カテゴリ</div>
        <div class="sell__category--box">
            @foreach($categories as $category)
                <!-- チェックボックス本体（画面上は非表示にする） -->
                <input type="checkbox" 
                    name="category_ids[]" 
                    value="{{ $category->id }}" 
                    id="category_{{ $category->id }}" 
                    class="sell__category--checkbox-hidden">
                
                <!-- ボタンとして表示するラベル -->
                <label for="category_{{ $category->id }}" class="sell__category--select">
                    {{ $category->category_name }}
                </label>
            @endforeach
        </div>

        <div>商品の状態</div>
        <div class="sell__select-box--wrapper">
            <!-- ⚠️注意点2：name属性が他ページの流用になっています -->
            <select class="sell__select-box" name="item_condition">
                <option value="1">良好</option>
                <option value="2">目立った傷や汚れなし</option>
                <option value="3">やや傷や汚れあり</option>
                <option value="4">状態が悪い</option>
            </select>
        </div>

        <div class="sell__section">商品と説明</div>
        <div>商品名</div>
        <input type="text" class="common__input-box" name="item_name">

        <div>ブランド名</div>
        <input type="text" class="common__input-box" name="brand_name">

        <div>商品の説明</div>
        <textarea class="common__input-textbox" name="item_detail"></textarea>

        <div>販売価格</div>
        <div class="sell__input-box--wrapper">
            <input type="text" class="common__input-box" name="item_price">
        </div>

        <!-- ⚠️注意点1：画像選択のJavaScript（onchange）に必須な隠し入力タグを追加 -->
        <input type="hidden" name="action" id="form-action" value="save_all">


        <!-- エラーがあればすべて箇条書きで表示するコード -->
        @if ($errors->any())
            <div style="color: red; margin: 20px 0;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <button type="submit" class="common__button" name="action" value="save_all">出品する</button>
    </div> <!-- .common__form をここで閉じる -->
</form> <!-- form を最後に閉じる -->
@endsection
