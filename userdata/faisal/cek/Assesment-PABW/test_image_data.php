<?php
// File untuk memeriksa data kampanye di database

require_once __DIR__.'/vendor/autoload.php';

// Set up Laravel environment
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\VolunteerCampaign;
use App\Models\Campaign;

echo "=== Memeriksa Data Kampanye Donasi ===\n";
$donationCampaigns = Campaign::limit(5)->get();
foreach ($donationCampaigns as $campaign) {
    echo "ID: {$campaign->id}, Title: {$campaign->title}\n";
    echo "Image DB Value: {$campaign->image}\n";
    echo "Image URL: {$campaign->image}\n";
    echo "Image Attribute: {$campaign->getOriginal('image')}\n";
    echo "---\n";
}

echo "\n=== Memeriksa Data Kampanye Relawan ===\n";
$volunteerCampaigns = VolunteerCampaign::limit(5)->get();
foreach ($volunteerCampaigns as $campaign) {
    echo "ID: {$campaign->id}, Judul: {$campaign->judul}\n";
    echo "Image DB Value: {$campaign->image}\n";
    echo "Image URL: {$campaign->image}\n";
    echo "Image Attribute: {$campaign->getOriginal('image')}\n";
    echo "---\n";
}