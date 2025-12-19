<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function show(string $code): \Illuminate\View\View
    {
        return view('jump/wechat');
    }
}
