<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

/**
*    public function edit(Request $request): View
*    {
*        // この画面を開いた「直前のURL」をセッションに保存する
*        if (!session()->has('from_url')) {
*            // 1. 直前の長いフルURLを取得する
*            $previousUrl = url()->previous();
*            
*            // 2. 初期値は空にしておく
*            $fromUrlValue = '';
*
*            // 3. URLの中に「verify-email」または「register」が含まれているかチェックして抜き出す
*            if (str_contains($previousUrl, 'verify-email')) {
*                $fromUrlValue = '/verify-email';
*            } elseif (str_contains($previousUrl, 'register')) {
*                $fromUrlValue = '/register';
*            }
*
*            // 4. 判定した綺麗な文字（/verify-email など）をセッションに保存する
*            session(['from_url' => $fromUrlValue]);
*        }
*        return view('profile.edit', [
*            'user' => $request->user(),
*        ]);
*    }
**/


    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
