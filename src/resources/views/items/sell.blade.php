@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/sell.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<div class="common__form">
    <h1>商品の出品</h1>

    {{-- ⭕ 1. 画像一時保存専用のフォーム（画面には見えません） --}}
    <form id="imageUploadForm" action="{{ route('sell.upload') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" id="hiddenItemImage" name="item_image" accept="image/*" style="display: none;" onchange="checkItemImageSize(this)">
    </form>

    {{-- ⭕ 2. 本番の出品保存専用のフォーム --}}
    <form action="{{ route('sell.store') }}" method="POST">
        @csrf

        <!-- 点線の枠エリア -->
        <div class="sell__photo--box">
            <div class="sell__photo--inner-container">
                <!-- 画像を選択するボタン -->
                <label style="cursor: pointer;" onclick="document.getElementById('hiddenItemImage').click();">
                    <div class="profile__picture--select">画像を選択する</div>
                </label>

                <!-- プレビュー画像表示 -->
                {{-- 💡 修正：コメントの開始と閉じを正しく修正しました --}}
                @if(session('item_tmp_image_path') || old('item_tmp_image_path'))
                    @php
                        $tmpPath = session('item_tmp_image_path') ?? old('item_tmp_image_path');
                        $tmpName = session('item_tmp_image_name') ?? old('item_tmp_image_name');
                    @endphp
                    <div class="sell__photo--preview-area">
                        @if($tmpName)
                            <div class="file-info-display">
                                {{-- 💡 修正：作成した変数 $tmpName を使うように変更 --}}
                                選択中のファイル: <strong>{{ $tmpName }}</strong>
                            </div>
                        @endif
                        <div class="preview-image-container">
                            {{-- 💡 修正：作成した変数 $tmpPath を使うように変更 --}}
                            <img src="/storage/{{ $tmpPath }}" class="common__picture--photo">
                        </div>
                        <!-- エラーで戻ってきたときも、この隠し入力（old）で値を次の送信へ引き継ぐ -->
                        <input type="hidden" name="item_tmp_image_path" value="{{ $tmpPath }}">
                        <input type="hidden" name="item_tmp_image_name" value="{{ $tmpName }}">
                    </div>
                @endif
            </div>
        </div>
        <div class="error-message">
            @error('item_image')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <div class="sell__section">商品の詳細</div>
        
        <div class="common__subsection">カテゴリ</div>
        <div class="sell__category--box">
            @foreach($categories as $category)
                <input type="checkbox" 
                    name="category_ids[]" 
                    value="{{ $category->id }}" 
                    id="category_{{ $category->id }}" 
                    class="sell__category--checkbox-hidden"
                    {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                <label for="category_{{ $category->id }}" class="sell__category--select">
                    {{ $category->category_name }}
                </label>
            @endforeach
        </div>
        <div class="error-message">
            @error('category_ids')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <div class="common__subsection">商品の状態</div>
        <div class="sell__select-box--wrapper">
        <select class="sell__select-box" name="condition">
            <!-- 💡 初期状態として「選択してください」を用意し、エラー時も選択を保持できるようにします -->
            <option value="" {{ old('condition') === null ? 'selected' : '' }} disabled>選択してください</option>
            
            <!-- 💡 モデルのアクセサ（match構文）の数値と完全に一致させました -->
            <option value="4" {{ old('condition') == '4' ? 'selected' : '' }}>良好</option>
            <option value="3" {{ old('condition') == '3' ? 'selected' : '' }}>目立った傷や汚れなし</option>
            <option value="2" {{ old('condition') == '2' ? 'selected' : '' }}>やや傷や汚れあり</option>
            <option value="1" {{ old('condition') == '1' ? 'selected' : '' }}>状態が悪い</option>
        </select>
        </div>
        <div class="error-message">
            @error('condition')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <div class="sell__section">商品と説明</div>
        
        <div class="common__subsection">商品名</div>
        <input type="text" class="common__input-box" name="item_name" value="{{ old('item_name') }}">
        <div class="error-message">
            @error('item_name')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <div class="common__subsection">ブランド名</div>
        <input type="text" class="common__input-box" name="brand_name" value="{{ old('brand_name') }}">

        <div class="common__subsection">商品の説明</div>
        <textarea class="common__input-textbox" name="item_detail">{{ old('item_detail') }}</textarea>
        <div class="error-message">
            @error('item_detail')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <div class="common__subsection">販売価格</div>
        <div class="sell__input-box--wrapper">
            <input type="text" class="common__input-box" name="item_price" value="{{ old('item_price') }}">
        </div>
        <div class="error-message">
            @error('item_price')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        {{-- ⭕ 普通のsubmitボタン。押すと100%確実に sell.store（本番保存）へ飛びます --}}
        <button type="submit" class="common__button">出品する</button>
    </form> 
</div>

<script>
function checkItemImageSize(input) {
    // 💡 .files[0] とすることで、選択された1番目のファイル本体を確実に取得します
    const file = input.files[0];

    if (file) {
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file.size > maxSize) {
            // 2MBを超えていた場合はアラートを出し、選択をクリアして処理を中断
            alert('画像サイズは2MB以内でアップロードしてください。');
            input.value = ''; // ファイルの選択をリセット
            return false;
        }
    }

    // 💡 2MB以内であれば、フォームを自動送信してプレビューさせます
    input.form.submit();
}
</script>


@endsection

