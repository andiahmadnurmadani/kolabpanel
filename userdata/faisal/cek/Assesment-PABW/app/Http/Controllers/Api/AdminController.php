<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\DonationTransaction;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics for admin
     */
    public function dashboardStats()
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        // Total campaigns
        $totalCampaigns = Campaign::count();
        $activeCampaigns = Campaign::where('status', 'Active')->count();

        // Total donations
        $totalDonations = DonationTransaction::count();
        $pendingDonations = DonationTransaction::where('status', 'PENDING_VERIFICATION')->count();
        $verifiedDonations = DonationTransaction::where('status', 'VERIFIED')->count();

        // Total amount of verified donations
        $totalAmount = DonationTransaction::where('status', 'VERIFIED')
            ->sum('amount');

        // Total users
        $totalUsers = User::count();
        $donaturUsers = User::where('role', 'donatur')->count();

        // Donations this month
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $monthlyDonations = DonationTransaction::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $monthlyAmount = DonationTransaction::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->where('status', 'VERIFIED')
            ->sum('amount');

        return response()->json([
            'data' => [
                'total_campaigns' => $totalCampaigns,
                'active_campaigns' => $activeCampaigns,
                'total_donations' => $totalDonations,
                'pending_donations' => $pendingDonations,
                'verified_donations' => $verifiedDonations,
                'total_amount' => $totalAmount,
                'total_users' => $totalUsers,
                'donatur_users' => $donaturUsers,
                'monthly_donations' => $monthlyDonations,
                'monthly_amount' => $monthlyAmount,
            ]
        ]);
    }

    /**
     * Get campaign statistics
     */
    public function campaignStats()
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        // Campaign by status
        $campaignsByStatus = Campaign::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Campaign by category
        $campaignsByCategory = Campaign::select('kategori', \DB::raw('count(*) as count'))
            ->groupBy('kategori')
            ->get();

        // Active campaigns by end date (closest to ending)
        $activeCampaigns = Campaign::where('status', 'Active')
            ->with(['user:id,name']) // Include user info
            ->select('id', 'title', 'description', 'target_amount', 'current_amount', 'image', 'end_date', 'user_id', 'status', 'kategori')
            ->orderBy('end_date', 'asc')
            ->limit(10) // 10 campaigns ending soonest
            ->get();

        return response()->json([
            'data' => [
                'by_status' => $campaignsByStatus,
                'by_category' => $campaignsByCategory,
                'ending_soon' => $activeCampaigns,
            ]
        ]);
    }

    /**
     * Get donation statistics
     */
    public function donationStats()
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        // Donations by status
        $donationsByStatus = DonationTransaction::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Donations by payment method
        $donationsByMethod = DonationTransaction::select('payment_method', \DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // Monthly donations for the current year
        $monthlyData = DonationTransaction::select(
                \DB::raw('MONTH(created_at) as month'),
                \DB::raw('COUNT(*) as count'),
                \DB::raw('SUM(amount) as total_amount')
            )
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'VERIFIED')
            ->groupBy(\DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        return response()->json([
            'data' => [
                'by_status' => $donationsByStatus,
                'by_method' => $donationsByMethod,
                'monthly_data' => $monthlyData,
            ]
        ]);
    }

    /**
     * Get user notifications
     */
    public function getNotifications(Request $request)
    {
        $user = auth()->user();

        // Jika role admin, bisa melihat semua notifikasi
        // Jika bukan admin, hanya bisa melihat notifikasi sendiri
        if ($user->role === 'admin') {
            // Dalam konteks admin API, kita mungkin ingin mengambil notifikasi untuk user tertentu
            $userId = $request->query('user_id');
            if ($userId) {
                $userNotifications = \App\Models\User::findOrFail($userId);
                $notifications = $userNotifications->notifications()->paginate(10);
            } else {
                // Secara default, kembalikan notifikasi milik admin itu sendiri
                $notifications = $user->notifications()->paginate(10);
            }
        } else {
            $notifications = $user->notifications()->paginate(10);
        }

        return response()->json([
            'data' => $notifications
        ]);
    }

    /**
     * Get user unread notifications count
     */
    public function getUnreadNotificationsCount()
    {
        $user = auth()->user();
        $unreadCount = $user->unreadNotifications->count();

        return response()->json([
            'data' => [
                'unread_count' => $unreadCount
            ]
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Mark specific notification as read
     */
    public function markAsRead($id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Send notification to users
     */
    public function sendNotification(Request $request)
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'url' => 'nullable|url',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id'
        ]);

        $userIds = $request->user_ids;

        // Jika tidak ada user_ids, kirim ke semua user
        if (!$userIds || empty($userIds)) {
            $users = \App\Models\User::all();
        } else {
            $users = \App\Models\User::whereIn('id', $userIds)->get();
        }

        foreach ($users as $user) {
            $user->notify(new \App\Notifications\CustomNotification(
                $request->title,
                $request->message,
                $request->icon ?? 'fas fa-bell',
                $request->color ?? 'text-blue-500',
                $request->url ?? '#'
            ));
        }

        return response()->json([
            'message' => 'Notifications sent successfully',
            'count' => count($users)
        ]);
    }

    /**
     * Get coin history for authenticated user
     */
    public function coinHistory()
    {
        $user = auth()->user();
        $coinHistory = $user->coinHistories()->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'data' => $coinHistory
        ]);
    }

    /**
     * Get coin history for specific user (admin only)
     */
    public function coinHistoryByUser($userId)
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $user = \App\Models\User::findOrFail($userId);
        $coinHistory = $user->coinHistories()->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'data' => $coinHistory,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'current_coins' => $user->coins
            ]
        ]);
    }

    /**
     * Get coin statistics
     */
    public function coinStats()
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        // Total coins earned by all users
        $totalCoins = \App\Models\CoinHistory::sum('amount');

        // Total users with coins
        $usersWithCoins = \App\Models\User::where('coins', '>', 0)->count();

        // Coins earned by type
        $coinsByType = \App\Models\CoinHistory::select('reason', \DB::raw('count(*) as count'), \DB::raw('sum(amount) as total_amount'))
            ->groupBy('reason')
            ->get();

        // Monthly coin distribution for current year
        $currentYear = \Carbon\Carbon::now()->year;
        $monthlyCoins = \App\Models\CoinHistory::select(
                \DB::raw('MONTH(created_at) as month'),
                \DB::raw('COUNT(*) as count'),
                \DB::raw('SUM(amount) as total_amount')
            )
            ->whereYear('created_at', $currentYear)
            ->groupBy(\DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        return response()->json([
            'data' => [
                'total_coins' => $totalCoins,
                'users_with_coins' => $usersWithCoins,
                'by_reason' => $coinsByType,
                'monthly_data' => $monthlyCoins,
            ]
        ]);
    }

    /**
     * Award coins to user (admin only)
     */
    public function awardCoins(Request $request, $userId)
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $request->validate([
            'amount' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $user = \App\Models\User::findOrFail($userId);

        // Add coins to user
        $user->coins += $request->amount;
        $user->save();

        // Create coin history record
        $user->coinHistories()->create([
            'amount' => $request->amount,
            'reason' => $request->reason,
            'transaction_type' => 'earned',
        ]);

        return response()->json([
            'message' => 'Coins awarded successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'coins' => $user->coins
            ]
        ]);
    }

    /**
     * Get donation detail for admin verification (with proof image)
     */
    public function donationDetail($orderId)
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $transaction = \App\Models\DonationTransaction::where('order_id', $orderId)
            ->with(['user', 'campaign'])
            ->first();

        if (!$transaction) {
            return response()->json([
                'message' => 'Donation not found'
            ], 404);
        }

        // Add proof image URL if proof exists
        if ($transaction->proof_of_transfer_path) {
            $transaction->proof_url = asset('storage/' . $transaction->proof_of_transfer_path);
        }

        return response()->json([
            'data' => $transaction
        ]);
    }

    /**
     * Update donation status (admin only)
     */
    public function updateDonationStatus(Request $request, $orderId)
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:AWAITING_TRANSFER,PENDING_VERIFICATION,VERIFIED,CANCELLED'
        ]);

        $transaction = \App\Models\DonationTransaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        $oldStatus = $transaction->status;
        $transaction->update(['status' => $request->status]);

        // If status is changing to VERIFIED (Paid) from a non-verified status, update campaign amount
        if ($request->status === 'VERIFIED' && $oldStatus !== 'VERIFIED' && $transaction->campaign_id) {
            // Add the donation amount to the campaign's current amount
            $campaign = \App\Models\Campaign::find($transaction->campaign_id);
            if ($campaign) {
                $campaign->increment('current_amount', $transaction->amount);
            }
        }
        // If status is changing from VERIFIED to something else, subtract the donation amount
        elseif ($oldStatus === 'VERIFIED' && $request->status !== 'VERIFIED' && $transaction->campaign_id) {
            $campaign = \App\Models\Campaign::find($transaction->campaign_id);
            if ($campaign) {
                $campaign->decrement('current_amount', $transaction->amount);
            }
        }

        // Award coins if transaction is marked as VERIFIED (Paid) and was previously not verified
        if ($request->status === 'VERIFIED' && $oldStatus !== 'VERIFIED' && $transaction->user) {
            $transaction->user->addCoins(1, 'donation_completed');
        }

        // Determine success message based on status
        $statusMessages = [
            'VERIFIED' => 'Payment successfully verified. Donation processed and kindness points added.',
            'CANCELLED' => 'Payment rejected. Donation cancelled.',
            'PENDING_VERIFICATION' => 'Payment status changed to pending verification.',
            'AWAITING_TRANSFER' => 'Payment status changed to awaiting transfer.'
        ];

        $message = $statusMessages[$request->status] ?? 'Transaction status updated successfully';

        return response()->json([
            'message' => $message,
            'data' => $transaction
        ]);
    }

    /**
     * Get all volunteers (admin only)
     */
    public function volunteers()
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $volunteers = \App\Models\Volunteer::with('campaign')->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'message' => 'Volunteers retrieved successfully',
            'data' => $volunteers
        ]);
    }

    /**
     * Get specific volunteer (admin only)
     */
    public function volunteer($id)
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $volunteer = \App\Models\Volunteer::with('campaign')->find($id);

        if (!$volunteer) {
            return response()->json([
                'message' => 'Volunteer not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Volunteer retrieved successfully',
            'data' => $volunteer
        ]);
    }

    /**
     * Create new volunteer (admin only)
     */
    public function createVolunteer(Request $request)
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'whatsapp' => 'required|string|max:20',
            'motivasi' => 'required|string',
            'keahlian' => 'required|string',
            'status_verifikasi' => 'required|in:pending,accepted,rejected',
            'volunteer_campaign_id' => 'required|exists:volunteer_campaigns,id'
        ]);

        $volunteer = \App\Models\Volunteer::create($request->all());

        // Update quota in volunteer campaign
        if ($volunteer->campaign) {
            $volunteer->campaign->increment('kuota_terisi');
        }

        return response()->json([
            'message' => 'Volunteer created successfully',
            'data' => $volunteer
        ], 201);
    }

    /**
     * Update volunteer (admin only)
     */
    public function updateVolunteer(Request $request, $id)
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $volunteer = \App\Models\Volunteer::find($id);

        if (!$volunteer) {
            return response()->json([
                'message' => 'Volunteer not found'
            ], 404);
        }

        $oldCampaignId = $volunteer->volunteer_campaign_id;

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'whatsapp' => 'required|string|max:20',
            'motivasi' => 'required|string',
            'keahlian' => 'required|string',
            'status_verifikasi' => 'required|in:pending,accepted,rejected',
            'volunteer_campaign_id' => 'required|exists:volunteer_campaigns,id'
        ]);

        $volunteer->update($request->all());

        // Update quota in old campaign if campaign changed
        if ($oldCampaignId && $oldCampaignId != $request->volunteer_campaign_id) {
            if ($volunteer->campaign) {
                $volunteer->campaign->increment('kuota_terisi');
            }
            $oldCampaign = \App\Models\VolunteerCampaign::find($oldCampaignId);
            if ($oldCampaign) {
                $oldCampaign->decrement('kuota_terisi');
            }
        }

        return response()->json([
            'message' => 'Volunteer updated successfully',
            'data' => $volunteer
        ]);
    }

    /**
     * Delete volunteer (admin only)
     */
    public function deleteVolunteer($id)
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $volunteer = \App\Models\Volunteer::find($id);

        if (!$volunteer) {
            return response()->json([
                'message' => 'Volunteer not found'
            ], 404);
        }

        $volunteerCampaign = $volunteer->campaign;
        $volunteer->delete();

        // Update quota in volunteer campaign
        if ($volunteerCampaign) {
            $volunteerCampaign->decrement('kuota_terisi');
        }

        return response()->json([
            'message' => 'Volunteer deleted successfully'
        ]);
    }

    /**
     * Get all volunteer campaigns for admin
     */
    public function adminVolunteerCampaigns()
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $volunteerCampaigns = \App\Models\VolunteerCampaign::orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'message' => 'Volunteer campaigns retrieved successfully',
            'data' => $volunteerCampaigns
        ]);
    }

    /**
     * Get specific volunteer campaign for admin
     */
    public function adminVolunteerCampaign($id)
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $volunteerCampaign = \App\Models\VolunteerCampaign::with('volunteers')->find($id);

        if (!$volunteerCampaign) {
            return response()->json([
                'message' => 'Volunteer campaign not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Volunteer campaign retrieved successfully',
            'data' => $volunteerCampaign
        ]);
    }

    /**
     * Get comprehensive dashboard statistics for admin
     */
    public function dashboardOverview()
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        // Total statistics
        $totalCampaigns = \App\Models\Campaign::count();
        $totalVolunteerCampaigns = \App\Models\VolunteerCampaign::count();
        $totalDonations = \App\Models\DonationTransaction::count();
        $totalVolunteers = \App\Models\Volunteer::count();
        $totalUsers = \App\Models\User::count();

        // Active campaigns
        $activeCampaigns = \App\Models\Campaign::where('status', 'Active')->count();
        $activeVolunteerCampaigns = \App\Models\VolunteerCampaign::where('status', 'Aktif')->count();

        // Recent activities
        $recentDonations = \App\Models\DonationTransaction::with('campaign', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentVolunteers = \App\Models\Volunteer::with('campaign')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Financial statistics
        $totalAmount = \App\Models\DonationTransaction::where('status', 'VERIFIED')->sum('amount');
        $monthlyAmount = \App\Models\DonationTransaction::where('status', 'VERIFIED')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // Status breakdown
        $donationStatuses = \App\Models\DonationTransaction::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $campaignStatuses = \App\Models\Campaign::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Monthly trend data for charts
        $monthlyDonationData = $this->getMonthlyDonationData();
        $monthlyCampaignData = $this->getMonthlyCampaignData();
        $monthlyVolunteerData = $this->getMonthlyVolunteerData();

        return response()->json([
            'message' => 'Dashboard overview retrieved successfully',
            'data' => [
                'totals' => [
                    'total_campaigns' => $totalCampaigns,
                    'total_volunteer_campaigns' => $totalVolunteerCampaigns,
                    'total_donations' => $totalDonations,
                    'total_volunteers' => $totalVolunteers,
                    'total_users' => $totalUsers,
                ],
                'active' => [
                    'active_campaigns' => $activeCampaigns,
                    'active_volunteer_campaigns' => $activeVolunteerCampaigns,
                ],
                'financial' => [
                    'total_amount' => $totalAmount,
                    'monthly_amount' => $monthlyAmount,
                ],
                'recent_activities' => [
                    'recent_donations' => $recentDonations,
                    'recent_volunteers' => $recentVolunteers,
                ],
                'breakdown' => [
                    'donation_statuses' => $donationStatuses,
                    'campaign_statuses' => $campaignStatuses,
                ],
                'chart_data' => [
                    'monthly_donations' => $monthlyDonationData,
                    'monthly_campaigns' => $monthlyCampaignData,
                    'monthly_volunteers' => $monthlyVolunteerData,
                ]
            ]
        ]);
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

            $verifiedAmount = \App\Models\DonationTransaction::where('status', 'VERIFIED')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');

            $totalDonations = \App\Models\DonationTransaction::whereYear('created_at', $month->year)
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

    /**
     * Get monthly campaign data for chart
     */
    private function getMonthlyCampaignData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->format('M Y');

            $campaigns = \App\Models\Campaign::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $activeCampaigns = \App\Models\Campaign::where('status', 'Active')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $data[] = [
                'month' => $monthName,
                'total' => $campaigns,
                'active' => $activeCampaigns
            ];
        }
        return $data;
    }

    /**
     * Get monthly volunteer data for chart
     */
    private function getMonthlyVolunteerData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->format('M Y');

            $volunteers = \App\Models\Volunteer::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $data[] = [
                'month' => $monthName,
                'count' => $volunteers
            ];
        }
        return $data;
    }
}
