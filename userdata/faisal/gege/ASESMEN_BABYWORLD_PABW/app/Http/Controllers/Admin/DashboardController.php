<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil filter dari request (default: 1 hari)
        $filter = request()->get('filter', 1);

        // Tentukan range tanggal
        switch ($filter) {
            case 7:
                $dateQuery = now()->subDays(7);
                break;
            case 30:
                $dateQuery = now()->subDays(30);
                break;
            case 365:
                $dateQuery = now()->subYear();
                break;
            default:
                $dateQuery = now()->startOfDay();
                break;
        }

        // --- CARD SUMMARY ---

        $totalProducts = DB::table('products')->count();

        $totalTransaksi = DB::table('tb_transaksi')->count();

        $totalPelanggan = DB::table('users')
            ->where('role', 'user')
            ->distinct('id')
            ->count('id');

        $totalUser = DB::table('users')->count();


        // --- SALES CHART DATA ---

        $sales = DB::table('tb_transaksi')
            ->select(DB::raw('MONTH(created_at) as bulan'), DB::raw('COUNT(*) as jumlah'))
            ->where('created_at', '>=', $dateQuery)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $salesMonths = $sales->pluck('bulan');
        $salesCounts = $sales->pluck('jumlah');


        // --- REVENUE CHART DATA ---

        $revenue = DB::table('tb_transaksi')
            ->select(DB::raw('MONTH(created_at) as bulan'), DB::raw('SUM(total_harga) as total'))
            ->where('created_at', '>=', $dateQuery)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $revenueMonths = $revenue->pluck('bulan');
        $revenueTotals = $revenue->pluck('total');


        // Kembalikan ke Blade
        return view('layouts.admin.dashboard', [
            'totalProducts' => $totalProducts,
            'totalTransaksi' => $totalTransaksi,
            'totalPelanggan' => $totalPelanggan,
            'totalUser' => $totalUser,

            'salesMonths' => $salesMonths,
            'salesCounts' => $salesCounts,

            'revenueMonths' => $revenueMonths,
            'revenueTotals' => $revenueTotals,

            'filter' => $filter,
        ]);
    }
}