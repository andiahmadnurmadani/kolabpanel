<?php
// File untuk memeriksa struktur data di database

require_once __DIR__.'/vendor/autoload.php';

// Set up Laravel environment
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\VolunteerCampaign;
use App\Models\Campaign;

echo "Memeriksa struktur data gambar...\n\n";

// Ambil beberapa contoh data kampanye donasi
$donationCampaigns = Campaign::limit(3)->get();
echo "Contoh data kampanye donasi:\n";
foreach ($donationCampaigns as $campaign) {
    echo "- ID: {$campaign->id}, Image: {$campaign->image}\n";
    echo "  Image URL: {$campaign->image}\n\n";
}

// Ambil beberapa contoh data kampanye relawan
$volunteerCampaigns = VolunteerCampaign::limit(3)->get();
echo "Contoh data kampanye relawan:\n";
foreach ($volunteerCampaigns as $campaign) {
    echo "- ID: {$campaign->id}, Image: {$campaign->image}\n";
    echo "  Image URL: {$campaign->image}\n\n";
}