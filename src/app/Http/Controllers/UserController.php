<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile(){
        $user = Auth::user();
        return view('auth.profile',compact('user'));
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
    public function updateAll(Request $request){
        $user = Auth::user();

        // パターンA：画像が選択されて自動リロードが入ったとき（一時保存処理）
        if ($request->input('action') === 'select_image') {
            
            // 画像の簡易バリデーション
            $request->validate([
                'member_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('member_image')) {
                $file = $request->file('member_image');
                $originalName = $file->getClientOriginalName();
                
                // 一時フォルダ（storage/app/public/tmp）に一旦保存
                $tmpPath = $file->store('tmp', 'public');

                // 入力中のテキスト（名前や住所）と、一時保存した画像情報を画面に突き返す
                return redirect()->back()
                    ->withInput() 
                    ->with([
                        'tmp_image_name' => $originalName,
                        'tmp_image_path' => $tmpPath
                    ]);
            }
            return redirect()->back()->withInput();
        }

        // パターンB：一番下の「更新する」ボタンが押されたとき（正式な最終保存）
        if ($request->input('action') === 'save_all') {
            
            // 1. テキスト情報（ユーザー名、郵便番号、住所、建物名）の更新
            $content = $request->only(['member_name', 'postcode', 'address', 'building']);
            $user->update($content);

            // 2. 一時保存された画像（tmp）があれば、本番フォルダへ移動して確定させる
            if ($request->filled('tmp_image_path')) {
                $tmpPath = $request->input('tmp_image_path');
                $fileName = time() . '_' . basename($tmpPath);
                $newPath = 'images/profile/' . $fileName;

                // ファイルを tmp から images/profile に移動
                if (Storage::disk('public')->exists($tmpPath)) {
                    
                    // ★【ここを修正】移動に成功する前に、古い本番画像があれば先に削除する
                    if ($user->member_image && $user->member_image !== 'silver.png') {
                        $oldImagePath = 'images/profile/' . $user->member_image;
                        
                        // 実際のフォルダに古いファイルが存在するか確認して削除
                        if (Storage::disk('public')->exists($oldImagePath)) {
                            Storage::disk('public')->delete($oldImagePath);
                        }
                    }

                    // 新しい画像を本番フォルダへ移動
                    Storage::disk('public')->move($tmpPath, $newPath);
                    
                    // データベースの画像名を新しいファイル名で更新
                    $user->member_image = $fileName;
                    $user->save();
                }
            }

            return redirect()->route('index')->with('success', 'プロフィールを更新しました！');
        }
    }
}
