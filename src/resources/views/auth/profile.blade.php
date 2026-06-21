@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<div class="common__form">
    <h1>プロフィール設定</h1>

    <!-- ★デバッグ用：現在のfrom_urlの状態を表示する -->
    <div style="background-color: #f0f8ff; border: 1px solid #1e90ff; padding: 10px; margin: 10px 0; border-radius: 5px;">
        <p style="margin: 0; font-size: 14px; color: #333;">
            現在のセッション(from_url): <strong>{{ session('from_url') ?? '空っぽ（無し）' }}</strong>
        </p>
        <p style="margin: 5px 0 0 0; font-size: 14px; color: #333;">
            現在のURLパラメータ(from_url): <strong>{{ request('from_url') ?? '空っぽ（無し）' }}</strong>
        </p>
    </div>

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
                            : '/storage/images/profile/silver.png') 
                }}" class="common__picture--photo">
                <div>
                    <div class="profile__picture--select">画像を選択する</div>
                    <!-- 「画像を選択する」をクリックしてファイルを選ぶとこの部分が動く
                    input type="file" でファイル選択ダイアログが開く
                     ファイル選択しOKボタン押すとonchangeで中身が変化したことをブラウザが検知
                     検知するとthis.form.submit()が実行され、画像をサーバに送って画面をリロードする
                     処理はUserControllerのupdateAllメソッドのパターンAで行われる -->
                    <input type="file" name="member_image" accept="image/*" style="display: none;" onchange=this.form.submit();>
                    <!-- フォーム送信時に from_url を確実にサーバーへ送るための命綱 -->
                    <input type="hidden" name="from_url" value="{{ session('from_url') ?? request('from_url') }}">

                    <!-- 画像ファイル設定した場合のファイル名表示エリア -->
                    @if(session('tmp_image_name'))
                        <div class="profile__file-info--display">
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