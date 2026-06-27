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
            // ① 認証済みなら、予定通り商品一覧画面（/）へ進む
            return redirect('/');
        }

        // ② まだ未認証なら、商品一覧へは行かせず、メール確認のお願い画面へ強制送還する！
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
