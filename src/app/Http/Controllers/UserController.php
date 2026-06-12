<?php

namespace App\Http\Controllers;

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

        // 画像選択のリロード時は記憶を上書きしないように条件分岐
        // 直前のURLが「自分自身（/profile）」ではない場合のみ、本当の前のURL（/registerなど）をセッションに保存
        if (!str_contains(url()->previous(), '/profile')) {
            session(['from_url' => url()->previous()]);
        }

        return view('auth.profile', compact('user'));
    }
    public function mypage(){
        if (Auth::check()) {
            $user = Auth::user();
            $items = Item::whereIn('sales_status', [1, 2, 3])->get();
            return view('auth.mypage', compact('user','items'));
        }
        $items = Item::whereIn('sales_status', [1, 2, 3])->get();
        return view('auth.mypage', compact('items'));
    }
    // ② 統合更新メソッド（修正）
    public function updateAll(Request $request){
        $user = Auth::user();

        // パターンA：画像が選択されて自動リロードが入ったとき（変更なし）
        if ($request->input('action') === 'select_image') {
            $request->validate([
                'member_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('member_image')) {
                $file = $request->file('member_image');
                $originalName = $file->getClientOriginalName();
                $tmpPath = $file->store('tmp', 'public');

                return redirect()->back()
                    ->withInput() 
                    ->with([
                        'tmp_image_name' => $originalName,
                        'tmp_image_path' => $tmpPath
                    ]);
            }
            return redirect()->back()->withInput();
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

            // ★【ここを修正】セッションから前のURLを取り出してルートを判定する
            $fromUrl = session('from_url', '');

            // 使い終わったセッションを削除して綺麗にする
            session()->forget('from_url');

            // URLに「/register」が含まれているかどうかで判定
            if (str_contains($fromUrl, '/register')) {
                return redirect()->route('index')->with('success', 'プロフィールを更新しました！');
            } else {
                return redirect()->route('mypage')->with('success', 'プロフィールを更新しました！');
            }
        }
    }
}
