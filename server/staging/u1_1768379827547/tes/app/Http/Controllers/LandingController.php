<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
/**
* Menampilkan Landing Page NotezQue.
* Dalam arsitektur MVC, Controller memuat data (jika ada) dan memanggil View.
*/
public function index()
{
    return view('landing.landing');
}

}