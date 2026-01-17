<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    // Menampilkan Dashboard Keuangan (Neraca Saldo)
    public function index()
    {
        // PERBAIKAN 1: Hitung pengeluaran berdasarkan status 'approved' DAN 'completed'
        // Ini memastikan data lama dan data baru tetap terhitung
        $campaigns = Campaign::withSum(['withdrawals' => function($query) {
            $query->whereIn('status', ['approved', 'completed']);
        }], 'amount')
        ->latest()
        ->paginate(10); 

        // Hitung Total Aset Yayasan (Global Stats)
        $totalDonasiMasuk = Campaign::sum('current_amount');
        
        // PERBAIKAN 2: Sinkronisasi rumus total pengeluaran global
        $totalPengeluaran = Withdrawal::whereIn('status', ['approved', 'completed'])->sum('amount');
        
        $totalSaldoTersedia = $totalDonasiMasuk - $totalPengeluaran;

        return view('admin.withdrawals.index', compact('campaigns', 'totalDonasiMasuk', 'totalPengeluaran', 'totalSaldoTersedia'));
    }

    // Mencatat Pengeluaran Baru
    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:1000',
            'expense_category' => 'required|string|max:100', 
            'custom_category' => 'nullable|string|max:100', 
            'description' => 'required|string|max:255', 
            'proof_file' => 'nullable|image|max:2048', 
        ]);

        $campaign = Campaign::findOrFail($request->campaign_id);
        $finalCategory = $request->expense_category;
        if ($finalCategory === 'Lainnya' && $request->filled('custom_category')) {
            $finalCategory = $request->custom_category;
        }

        $usedFunds = $campaign->withdrawals()
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount'); 
        $currentBalance = $campaign->current_amount - $usedFunds;

        if ($request->amount > $currentBalance) {
            return back()->with('error', 'Gagal! Saldo kampanye tidak mencukupi.');
        }

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('withdrawal-proofs', 'public');
        }

        Withdrawal::create([
            'campaign_id' => $campaign->id,
            'user_id' => Auth::id(), 
            'amount' => $request->amount,
            'bank_name' => 'EXPENSE', 
            'account_number' => $finalCategory, // Simpan kategori final di sini
            'account_holder_name' => $request->description, 
            'status' => 'approved', 
            'transferred_at' => now(),
            'proof_file' => $proofPath,
            'admin_note' => 'Pengeluaran dicatat manual oleh Admin.',
        ]);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }
    // Menampilkan Riwayat Transaksi Per Kampanye
    public function history($id)
    {
        $campaign = Campaign::with(['withdrawals' => function($query) {
            $query->whereIn('status', ['approved', 'completed'])->latest();
        }])->findOrFail($id);

        // Hitung ulang saldo spesifik kampanye ini
        $totalIn = $campaign->current_amount;
        $totalOut = $campaign->withdrawals->sum('amount');
        $balance = $totalIn - $totalOut;

        return view('admin.withdrawals.history', compact('campaign', 'totalIn', 'totalOut', 'balance'));
    }
}