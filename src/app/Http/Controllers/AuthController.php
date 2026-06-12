<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function loginView(){
        return view('auth.login');
    }
    public function login(LoginRequest $request){
        // 1. 画面から送られてきたメールアドレスとパスワードを取得
        $credentials = $request->only('email', 'password');

        // 2. ログイン（認証）を試みる
        if (Auth::attempt($credentials)) {
            // ログイン成功：セッションを新しくしてセキュリティを高める
            $request->session()->regenerate();
            // ログインしたユーザー（Auth::user()）の last_login_at に現在時刻（now()）を保存
            Auth::user()->update([
                'last_login_at' => now(),
            ]);
            return redirect()->intended('/');
            return back()->withErrors([
                'auth_error' => 'ログイン情報が登録されていません',
            ])->onlyInput('email');
        }

        // 3. ログイン失敗：エラーメッセージを伴ってログイン画面に戻す
        return back()->withErrors([
            'password' => 'ログイン情報が登録されていません',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // 1. ログアウト処理を実行
        Auth::logout();

        // 2. セッションを無効化（安全のため）
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 3. 【ここが重要】マイページ（/mypage）に自動で移動させる
        return redirect('/'); 
    }

    
    public function create(){
        return view('auth.register');
    }
    public function store(RegisterRequest $request){
        $content=$request->only(['member_name','email','password']);
        $content['password'] = Hash::make($content['password']);
        $user=User::create($content);
        Auth::login($user);
        return redirect()->route('profile');
    }
}
