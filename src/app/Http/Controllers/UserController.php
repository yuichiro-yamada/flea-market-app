<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile(){
        $user = Auth::user();
        return view('auth.profile',compact('user'));
    }
    public function update(Request $request){
        $content=$request->only(['member_name','postcode','address','building']);
        Auth::user()->update($content);
        return redirect()->route('mypage');
    }
    public function mypage(){
        if (Auth::check()) {
            $user = Auth::user(); 
            return view('auth.mypage', compact('user'));
        }
        return view('auth.mypage');
    }
}
