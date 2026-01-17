<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Campaign;
use App\Models\VolunteerCampaign;
use App\Models\DonationTransaction;
use App\Models\Volunteer;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Total statistics
        $totalCampaigns = Campaign::count();
        $totalVolunteerCampaigns = VolunteerCampaign::count();
        $totalDonations = DonationTransaction::count();
        $totalVolunteers = Volunteer::count();
        $totalUsers = User::count();

        // Active campaigns
        $activeCampaigns = Campaign::where('status', 'Active')->count();
        $activeVolunteerCampaigns = VolunteerCampaign::where('status', 'Aktif')->count();

        // Recent activities
        $recentDonations = DonationTransaction::with('campaign', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentVolunteers = Volunteer::with('campaign')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Financial statistics
        $totalAmount = DonationTransaction::where('status', 'VERIFIED')->sum('amount');
        $monthlyAmount = DonationTransaction::where('status', 'VERIFIED')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // Status breakdown
        $donationStatuses = DonationTransaction::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $campaignStatuses = Campaign::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Pending transactions that need verification
        $pendingTransactions = DonationTransaction::where('status', 'PENDING')->count();

        // Monthly trend data for charts
        $monthlyDonationData = $this->getMonthlyDonationData();

        return view('admin.dashboard', compact(
            'totalCampaigns',
            'totalVolunteerCampaigns', 
            'totalDonations',
            'totalVolunteers',
            'totalUsers',
            'activeCampaigns',
            'activeVolunteerCampaigns',
            'totalAmount',
            'monthlyAmount',
            'recentDonations',
            'recentVolunteers',
            'donationStatuses',
            'campaignStatuses',
            'pendingTransactions',
            'monthlyDonationData'
        ));
    }

    /**
     * Get monthly donation data for chart
     */
    private function getMonthlyDonationData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->format('M Y');

            $verifiedAmount = DonationTransaction::where('status', 'VERIFIED')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');

            $totalDonations = DonationTransaction::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $data[] = [
                'month' => $monthName,
                'amount' => $verifiedAmount,
                'count' => $totalDonations
            ];
        }
        return $data;
    }
}