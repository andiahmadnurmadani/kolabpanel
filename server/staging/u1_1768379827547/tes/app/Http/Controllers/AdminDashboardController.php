<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Note;
use App\Models\Activity;
use App\Models\Task;
use App\Models\KontenStatis;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = User::count();
        $activeUsers = User::whereNotNull('last_login_at')
                          ->where('last_login_at', '>=', now()->subDays(7))
                          ->count();
        
        $totalNotes = Note::count();
        $totalActivities = Activity::count() + Task::count();
        
        // Recent users (last 5)
        $recentUsers = User::orderBy('created_at', 'desc')
                          ->take(5)
                          ->get();

        // Konten statis
        $kontenStatis = KontenStatis::pluck('value', 'key');

        return view('admin.dashboard', compact(
            'totalUsers', 
            'activeUsers', 
            'totalNotes', 
            'totalActivities',
            'recentUsers',
            'kontenStatis'
        ));
    }
}
