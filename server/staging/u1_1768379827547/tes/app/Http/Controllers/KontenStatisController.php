<?php

namespace App\Http\Controllers;

use App\Models\KontenStatis;
use Illuminate\Http\Request;

class KontenStatisController extends Controller
{
    /**
     * Halaman untuk melihat konten statis (untuk user)
     */
    public function index()
    {
        $konten = KontenStatis::orderBy('key')->get();
        return view('pages.konten.index', compact('konten'));
    }

    /**
     * Menampilkan detail konten statis
     */
    public function show($key)
    {
        $konten = KontenStatis::where('key', $key)->firstOrFail();
        return view('pages.konten.show', compact('konten'));
    }
}
