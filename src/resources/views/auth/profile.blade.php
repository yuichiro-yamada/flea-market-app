@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<div class="common__form">
    <h1>プロフィール設定</h1>
    <!-- ① actionを「/profile/update」に変更、② 画像送信用の enctype を追加、③ PATCHは削除（POSTで処理します） -->
    <form action="{{ route('profile.update.all') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        
        <div>
            <label style="cursor: pointer;" class="profile__picture">
                <!-- ★【変更】一時保存された画像があればそれを、なければ現在の画像（またはデフォルト）を表示 -->
                <img src="{{ 
                    session('tmp_image_path') && file_exists(public_path('storage/' . session('tmp_image_path')))
                        ? '/storage/' . session('tmp_image_path') 
                        : ($user->member_image && file_exists(public_path('storage/images/profile/' . $user->member_image))
                            ? '/storage/images/profile/' . $user->member_image 
                            : '/images/profile/silver.png') 
                }}" class="common__picture--photo">
                <div>
                    <div class="profile__picture--select">画像を選択する</div>
                    <input type="file" name="member_image" accept="image/*" style="display: none;" onchange="this.form.submit()">
                    <!-- ファイル名とパスの表示エリア -->
                    @if(session('tmp_image_name'))
                        <div class="file-info-display" style="margin-top: 10px; font-size: 14px; color: #555;">
                            選択中のファイル: <strong>{{ session('tmp_image_name') }}</strong>
                            <input type="hidden" name="tmp_image_path" value="{{ session('tmp_image_path') }}">
                        </div>
                    @endif
                </div>

            </label>

            <!-- 自動リロードされた時に、コントローラ側に「画像選択の処理」だと伝えるための隠しタグ -->
            <input type="hidden" name="action" value="select_image">


        </div>

        <!-- ★【変更】value属性を「old()」に変更。これで画像選択時の自動リロードでも入力内容が消えません -->
        <div class="common__">ユーザー名</div>
        <input type="text" class="common__input-box" name="member_name" value="{{ old('member_name', $user->member_name) }}">
        
        <div>郵便番号</div>
        <input type="text" class="common__input-box" name="postcode" value="{{ old('postcode', $user->postcode) }}">
        
        <div>住所</div>
        <input type="text" class="common__input-box" name="address" value="{{ old('address', $user->address) }}">
        
        <div>建物名</div>
        <input type="text" class="common__input-box" name="building" value="{{ old('building', $user->building) }}">
        
        <!-- ★【変更】一番下のボタンに「最終保存」だと判別するための name と value を追加 -->
        <button class="common__button" type="submit" name="action" value="save_all">更新する</button>
    </form>

</div>
@endsection