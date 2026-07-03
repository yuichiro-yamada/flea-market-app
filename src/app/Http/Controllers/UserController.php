<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // ① プロフィール画面を表示するメソッド（修正）
    public function profile(){
        $user = Auth::user();

        // 1. 直前の長いフルURLを取得する
        $previousUrl = url()->previous();

        // 2. 直前のURLが「自分自身（profile）」を含まない、本当の移動元である時だけ記憶する
        if (!str_contains($previousUrl, 'profile')) {
            
            $fromUrlValue = '';

            // 長い認証URLや登録URLの中から、キーワードだけを確実に抜き出す
            if (str_contains($previousUrl, 'verify-email')) {
                $fromUrlValue = '/verify-email';
            } elseif (str_contains($previousUrl, 'register')) {
                $fromUrlValue = '/register';
            }

            // 綺麗な文字（/verify-email や /register）をセッションにカチッと保存する
            session(['from_url' => $fromUrlValue]);
        }

        return view('auth.profile', compact('user'));
    }

    public function mypage(Request $request)
    {
        $user = Auth::user();   // ログインユーザー情報の取得
        $myId = Auth::id(); // ログイン中のユーザーID
        $page = $request->query('page', 'sell'); // デフォルトは 'sell'

        if ($page === 'buy') {
            $items = Item::where('buyer_id', $myId)->latest()->get();
        } else {
            $items = Item::where('seller_id', $myId)->latest()->get();
        }

        return view('auth.mypage', compact('items', 'user'));
    }
    // ② 統合更新メソッド（修正）
    public function updateAll(ProfileUpdateRequest $request){
        $user = Auth::user();

        // パターンA：画像が選択されて自動リロードが入ったとき
        if ($request->input('action') === 'select_image') {
            $request->session()->forget('_old_input');

            $request->validate([
                'member_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('member_image')) {
                $file = $request->file('member_image');
                $originalName = $file->getClientOriginalName();
                $tmpPath = $file->store('tmp', 'public');

                // 💡 さっき追加した session()->put('_old_input...') の2行は削除して、すっきりさせて大丈夫です

                return redirect()->back()
                    ->with([
                        'tmp_image_name' => $originalName,
                        'tmp_image_path' => $tmpPath,
                        'from_url' => $request->input('from_url')
                    ]);
            }
            return redirect()->back();
        }


        // パターンB：一番下の「更新する」ボタンが押されたとき（リダイレクト先を修正）
        if ($request->input('action') === 'save_all') {
            
            $content = $request->only(['member_name', 'postcode', 'address', 'building']);
            $user->update($content);

            if ($request->filled('tmp_image_path')) {
                $tmpPath = $request->input('tmp_image_path');
                $fileName = time() . '_' . basename($tmpPath);
                $newPath = 'images/profile/' . $fileName;

                if (Storage::disk('public')->exists($tmpPath)) {
                    if ($user->member_image && $user->member_image !== 'silver.png') {
                        $oldImagePath = 'images/profile/' . $user->member_image;
                        if (Storage::disk('public')->exists($oldImagePath)) {
                            Storage::disk('public')->delete($oldImagePath);
                        }
                    }
                    Storage::disk('public')->move($tmpPath, $newPath);
                    $user->member_image = $fileName;
                    $user->save();
                }
            }

            // form送信内容に（hiddenで埋め込まれた）from_urlがあるかを確認、
            // なければsessionのform_urlの値を使う
            // （セッションでの情報受渡は不安定、formの受け渡しの方が確実）
            $fromUrl = $request->input('from_url') ?: session('from_url', '');

            // 使い終わったセッションを削除して綺麗にする
            session()->forget('from_url');

            // URLに「/register」または「/verify-email」が含まれているかどうかで判定
            if (str_contains($fromUrl, 'register') || str_contains($fromUrl, 'verify-email')) {
                return redirect()->route('index')->with('success', 'プロフィールを更新しました！');
            } else {
                return redirect()->route('mypage')->with('success', 'プロフィールを更新しました！');
            }
        }
    }
}

