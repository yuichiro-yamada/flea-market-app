<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(){
        return view('auth.profile');
    }
    public function mypage(){
        return view('auth.mypage');
    }
}
