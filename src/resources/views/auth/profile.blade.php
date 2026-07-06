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
            <div class="profile__picture">
                <!-- 💡 上部エリア：一時保存パスを最優先で表示 -->
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
                    {{-- 💡 登録されたfor属性だけで綺麗に連動させます --}}
                    <label for="member_image" class="profile__picture--select" style="cursor: pointer;">画像を選択する</label>
                    
                    <!-- 
                      【プロフィール画像選択・自動プレビューの仕組み】
                      1. 「画像を選択する」をクリックすると、labelのfor属性によって下の input(type="file") が1回で連動して開く。
                      2. ファイルを選択して決定すると、onchangeによって「checkAndSubmit()」関数が起動。
                      3. JavaScript側で「2MBの容量制限」をチェックし、問題なければ「action」に「select_image」の値をセットした上で、フォームを自動送信（submit）する。
                      4. 処理は UserController の updateAllメソッド（パターンA）で行われ、画像をtmpフォルダへ一時保存した後にセッション（またはold）にパスを持たせて画面を自動リロードする。
                      5. 画面上部では、一時保存パスの有無を最優先で判定しているため、バリデーションエラー等で画面が戻ってきた（oldに入った）際にも選択した画像が消えずに維持される。
                    -->

                    <!-- 1. アクションの目印（JavaScriptが送信直前に動的に書き換えます。初期値は空で正解です） -->
                    <input type="hidden" id="form_action" name="action" value="">

                    <!-- 2. ファイル選択（ユーザーの操作用） -->
                    <input type="file" id="member_image" name="member_image" accept="image/*" style="display: none;" onchange="checkAndSubmit(this)">

                    <!-- 3. 元いたURL of 記憶 -->
                    <input type="hidden" name="from_url" value="{{ session('from_url') ?? request('from_url') }}">        
                    
                    <div class="error-message">
                        @error('member_image')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror
                    </div>
                
                    {{-- 💡 エラーで戻ってきた時（old）もファイル名と隠しパスをキープ --}}
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

        <!-- ★【変更】value属性を「old()」に変更。これで画像選択時の自動リロードでも入力内容が消えません -->
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

        <!-- ★【変更】一番下のボタンに「最終保存」だと判別するための name と value を追加 -->
        <button class="common__button" type="submit" name="action" value="save_all">更新する</button>
    </form>

</div>

<script>
function checkAndSubmit(input) {
    // 💡 1. 選択された1番目のファイル本体を確実に取得
    const file = input.files[0];

    if (file) {
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file.size > maxSize) {
            alert('画像サイズは2MB以内でアップロードしてください。');
            input.value = ''; // 2MBを超えていたら選択をクリア
            return false;
        }
    } else {
        // ファイルが選択されていない（キャンセルされた）場合は何もしない
        return false;
    }

    // 💡 2. コントローラに「画像選択の処理」だと伝える目印をセット
    document.getElementById('form_action').value = 'select_image';

    // 💡 3. フォームの自動送信を実行
    input.form.submit();
    
    // 💡 4. 送信完了直後に、input自体の選択を一度リセットする（選び直しのバグを防ぐ保険）
    // これにより、ブラウザが「毎回新しく画像が選ばれた」と認識できるようになります
    setTimeout(() => {
        input.value = '';
    }, 100);
}
</script>

@endsection