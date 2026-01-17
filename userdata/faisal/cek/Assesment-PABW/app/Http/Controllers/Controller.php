<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Campaign;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function home()
    {
        // Get all active campaigns from the database with necessary relationships (will be limited in the view)
        $campaigns = Campaign::where('status', 'Active')
            ->select(['id', 'title', 'description', 'image', 'target_amount', 'current_amount', 'end_date', 'status', 'kategori', 'slug'])
            ->get();

        // Get urgent campaigns: campaigns that have less than 7 days remaining (regardless of funding level)
        $urgentCampaigns = Campaign::where('status', 'Active')
            ->select(['id', 'title', 'description', 'image', 'target_amount', 'current_amount', 'end_date', 'status', 'kategori', 'slug'])
            ->whereRaw('DATEDIFF(end_date, NOW()) <= 7')
            ->whereRaw('DATEDIFF(end_date, NOW()) >= 0') // Ensure end date is not in the past
            ->orderByRaw('DATEDIFF(end_date, NOW()) ASC')
            ->orderBy('current_amount', 'desc')
            ->limit(4)
            ->get();

        // Get active volunteer campaigns from the database
        $volunteerCampaigns = \App\Models\VolunteerCampaign::where('status', 'Aktif')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('home', compact('campaigns', 'urgentCampaigns', 'volunteerCampaigns'));
    }
}
