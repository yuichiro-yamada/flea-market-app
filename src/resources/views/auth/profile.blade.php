@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<div class="common__form">
    <h1>プロフィール設定</h1>
    <form action="{{ route('profile.update.all') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        <div>
            <div class="profile__picture">
                {{-- 上部エリア：一時保存パスを最優先で表示 --}}
                <div class="common__picture">
                    @if(session()->has('tmp_image_path'))
                        <img src="/storage/{{ session()->get('tmp_image_path') }}" class="common__picture--photo">
                    @elseif(old('tmp_image_path'))
                        <img src="/storage/{{ old('tmp_image_path') }}" class="common__picture--photo">
                    @else
                        <img src="{{ 
                            $user->member_image && file_exists(public_path('storage/images/profile/' . $user->member_image))
                                ? '/storage/images/profile/' . $user->member_image 
                                : '/storage/images/profile/silver.png'
                        }}" class="common__picture--photo">
                    @endif
                </div>

                <div>
                    {{-- labelのfor属性とinputを紐付け、画像選択ボタンとして表示 --}}
                    <label for="member_image" class="profile__picture--select" style="cursor: pointer;">画像を選択する</label>
                    
                    <!--
                    画像選択時の処理フロー
                    ・ファイル選択後、JavaScriptで容量チェックを実行
                    ・問題がなければ画像選択用のactionを設定してフォーム送信
                    ・選択画像は一時保存し、画面再表示後も選択状態を維持する
                    -->

                    {{-- 送信処理の種類を判別するためのhidden項目 --}}
                    <input type="hidden" id="form_action" name="action" value="">

                    {{-- labelクリック時に開くファイル選択用input --}}
                    <input type="file" id="member_image" name="member_image" accept="image/*" style="display: none;" onchange="checkAndSubmit(this)">

                    {{-- 画像選択後の画面再表示時に元のURLを保持 --}}
                    <input type="hidden" name="from_url" value="{{ session('from_url') ?? request('from_url') }}">        
                    
                    <div class="error-message">
                        @error('member_image')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror
                    </div>
                
                    {{-- バリデーションエラー時でも一時保存した画像情報を維持 --}}
                    @if(session()->has('tmp_image_name'))
                        <div class="profile__file-info--display">
                            選択中のファイル: <strong>{{ session()->get('tmp_image_name') }}</strong>
                            <input type="hidden" name="tmp_image_name" value="{{ session()->get('tmp_image_name') }}">
                            <input type="hidden" name="tmp_image_path" value="{{ session()->get('tmp_image_path') }}">
                        </div>
                    @elseif(old('tmp_image_name'))
                        <div class="profile__file-info--display">
                            選択中のファイル: <strong>{{ old('tmp_image_name') }}</strong>
                            <input type="hidden" name="tmp_image_name" value="{{ old('tmp_image_name') }}">
                            <input type="hidden" name="tmp_image_path" value="{{ old('tmp_image_path') }}">
                        </div>
                    @endif
                </div>
            </div> 
        </div>

        <!--
        old()を優先して表示することで、
        画像選択によるフォーム再送信後も入力済みデータを保持する
        -->
        <div class="common__subsection">ユーザー名</div>
        <input type="text" class="common__input-box" name="member_name" value="{{ old('member_name', session('member_name', $user->member_name)) }}">
        <div class="error-message">
            @error('member_name')
            {{$message}}
            @enderror
        </div>
        
        <div class="common__subsection">郵便番号</div>
        <input type="text" class="common__input-box" name="postcode" value="{{ old('postcode', session('postcode', $user->postcode)) }}">
        <div class="error-message">
            @error('postcode')
            {{$message}}
            @enderror
        </div>

        <div class="common__subsection">住所</div>
        <input type="text" class="common__input-box" name="address" value="{{ old('address', session('address', $user->address)) }}">
        <div class="error-message">
            @error('address')
            {{$message}}
            @enderror
        </div>

        <div class="common__subsection">建物名</div>
        <input type="text" class="common__input-box" name="building" value="{{ old('building', session('building', $user->building)) }}">
        <div class="error-message">
            @error('building')
            {{$message}}
            @enderror
        </div>

        {{-- 通常更新と画像選択処理を区別するため、最終保存時のaction値を設定 --}}
        <button class="common__button" type="submit" name="action" value="save_all">更新する</button>
    </form>

</div>

<script>
function checkAndSubmit(input) {
    // 1. 選択されたファイル本体を取得
    const file = input.files[0];

    if (file) {
        const maxSize = 2 * 1024 * 1024;

        if (file.size > maxSize) {
            alert('画像サイズは2MB以内でアップロードしてください。');
            input.value = ''; // 2MBを超えていたら選択をクリア
            return false;
        }
    } else {
        // ファイルが選択されていない（キャンセルされた）場合は何もしない
        return false;
    }

    // 2. コントローラに「画像選択の処理」だと伝える目印をセット
    document.getElementById('form_action').value = 'select_image';

    // 3. フォームの自動送信を実行
    input.form.submit();
    
    // 4. 送信完了直後に、input自体の選択を一度リセットする（選び直しのバグを防ぐ保険）
    // これにより、ブラウザが「毎回新しく画像が選ばれた」と認識できる
    setTimeout(() => {
        input.value = '';
    }, 100);
}
</script>

@endsection