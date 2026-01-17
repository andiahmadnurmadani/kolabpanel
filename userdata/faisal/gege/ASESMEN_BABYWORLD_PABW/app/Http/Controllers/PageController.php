<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function accounts()
    {
        return view('accounts');
    }

    public function compare()
    {
        return view('compare');
    }

    public function contact()
    {
        return view('contact');
    }

    public function wishlist()
    {
        return view('wishlist');
    }

    public function cart()
    {
        return view('cart');
    }

    public function chat()
    {
        return view('chat');
    }
}
