<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Acara mendatang (dibatasi 3 item)
        $acaraMendatang = $user->activities()
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->orderBy('time')
            ->take(3)
            ->get();

        return view('pages.dash', compact('acaraMendatang'));
    }
}
