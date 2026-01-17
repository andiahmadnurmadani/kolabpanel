<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if SQLite is being used
        $driverName = DB::getDriverName();

        $campaigns = DB::table('volunteer_campaigns')->orderBy('id')->get();

        if ($driverName === 'sqlite') {
            // For SQLite - disable foreign key constraints temporarily
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            // For MySQL - disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Create mapping of old IDs to new sequential IDs
        $volunteerCampaignMap = [];
        foreach ($campaigns as $index => $campaign) {
            $newId = $index + 1;
            $volunteerCampaignMap[$campaign->id] = $newId;
        }

        // Get all volunteers with their current campaign associations
        $volunteers = DB::table('volunteers')->get();

        // Clear all volunteer_campaigns records
        if ($driverName === 'sqlite') {
            DB::statement('DELETE FROM volunteer_campaigns;');
        } else {
            DB::table('volunteer_campaigns')->truncate();
        }

        // Re-insert campaigns with new sequential IDs
        foreach ($campaigns as $index => $campaign) {
            $newId = $index + 1;
            DB::table('volunteer_campaigns')->insert([
                'id' => $newId,
                'judul' => $campaign->judul,
                'slug' => \Illuminate\Support\Str::slug($campaign->judul),
                'lokasi' => $campaign->lokasi,
                'tanggal_mulai' => $campaign->tanggal_mulai,
                'tanggal_selesai' => $campaign->tanggal_selesai,
                'status' => $campaign->status,
                'deskripsi' => $campaign->deskripsi ?? 'Deskripsi kampanye sukarelawan',
                'kategori' => $campaign->kategori ?? 'Sosial',
                'kuota_total' => $campaign->kuota_total ?? 50,
                'kuota_terisi' => $campaign->kuota_terisi ?? 0,
                'created_at' => $campaign->created_at,
                'updated_at' => $campaign->updated_at,
            ]);
        }

        // Update the volunteer records with new campaign IDs
        foreach ($volunteers as $volunteer) {
            if ($volunteer->volunteer_campaign_id && isset($volunteerCampaignMap[$volunteer->volunteer_campaign_id])) {
                $newCampaignId = $volunteerCampaignMap[$volunteer->volunteer_campaign_id];
                DB::table('volunteers')
                    ->where('id', $volunteer->id)
                    ->update(['volunteer_campaign_id' => $newCampaignId]);
            }
        }

        if ($driverName === 'sqlite') {
            // Re-enable foreign key enforcement for SQLite
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            // Re-enable foreign key checks for MySQL
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For the down migration, we would need to keep track of the original IDs
        // Since this is a complex operation that might not be easily reversible,
        // we'll add a warning and potentially leave this migration as irreversible
        throw new \Exception('This migration cannot be reverted. Please restore from backup.');
    }
};
