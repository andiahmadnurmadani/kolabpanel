<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Note;
use App\Models\Task;
use App\Models\Activity;
use App\Models\KontenStatis;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatistikController extends Controller
{
    public function index(Request $req)
    {
        // Total statistik
        $totalUsers = User::count();
        $totalAdmins = User::where('is_admin', true)->count();
        $totalNonAdmins = $totalUsers - $totalAdmins;
        
        // User aktif (login dalam 7 hari terakhir)
        $activeUsers = User::where('updated_at', '>=', now()->subDays(7))->count();
        
        // Total konten
        $totalNotes = Note::count();
        $totalTasks = Task::count();
        $totalActivities = Activity::count();
        
        // Task statistics
        $completedTasks = Task::where('status', 'completed')->count();
        $pendingTasks = Task::where('status', 'pending')->count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();
        
        // Activity statistics
        $completedActivities = Activity::where('status', 'selesai')->count();
        $pendingActivities = Activity::where('status', 'pending')->count();
        
        // User registration trend (last 30 days)
        $userTrend = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Notes creation trend (last 30 days)
        $notesTrend = Note::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Tasks creation trend (last 30 days)
        $tasksTrend = Task::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Top 5 users by content
        $topUsers = User::withCount(['notes', 'tasks', 'activities'])
            ->orderBy('notes_count', 'desc')
            ->take(5)
            ->get();

        // Konten statis
        $kontenStatis = KontenStatis::pluck('value', 'key');

        return view('admin.statistik.index', compact(
            'totalUsers',
            'totalAdmins',
            'totalNonAdmins',
            'activeUsers',
            'totalNotes',
            'totalTasks',
            'totalActivities',
            'completedTasks',
            'pendingTasks',
            'inProgressTasks',
            'completedActivities',
            'pendingActivities',
            'userTrend',
            'notesTrend',
            'tasksTrend',
            'topUsers',
            'kontenStatis'
        ));
    }
}
