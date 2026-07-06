<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // ログインしたユーザーを取得してカラムをnullにする
        $user = auth()->user();
        $user->update([
            'shipping_postcode' => null,
            'shipping_address' => null,
            'shipping_building' => null,
        ]);

        // ログインしたユーザーが、メール認証を「すでに済ませているか」をチェック
        if ($request->user()->hasVerifiedEmail()) {
            // もし引き継がれた「url」があれば、そこへ最優先で戻す
            if ($request->filled('url')) {
                return redirect($request->input('url'));
            }
            // 認証済みなら、「/」または遷移しようとしていた画面へ遷移
            return redirect()->intended(route('index', absolute: false));
        }

        // まだ未認証なら、メール確認のお願い画面へ強制送還する
        return redirect()->route('verification.notice');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
