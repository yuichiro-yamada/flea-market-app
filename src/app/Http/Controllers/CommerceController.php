<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommerceController extends Controller
{
    public function sell(){
        return view('sell');
    }
    public function purchase(){
        return view('purchase');
    }

}
