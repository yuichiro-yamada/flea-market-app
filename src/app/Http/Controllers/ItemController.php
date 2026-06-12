<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(){
        if (Auth::check()) {
            $user = Auth::user();
            $items = Item::whereIn('sales_status', [1, 2, 3])->get();
            return view('auth.mypage', compact('user','items'));
        }
        $items = Item::whereIn('sales_status', [1, 2, 3])->get();
        return view('/index', compact('items'));
    }
}
