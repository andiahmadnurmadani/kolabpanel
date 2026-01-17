<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RecalculateCampaignAmounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:recalculate-amounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate current amounts for all campaigns based on verified donations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaigns = \App\Models\Campaign::all();

        $this->info('Starting campaign amount recalculation...');
        $this->info('Found ' . $campaigns->count() . ' campaigns to process.');

        $bar = $this->output->createProgressBar($campaigns->count());
        $bar->start();

        foreach ($campaigns as $campaign) {
            // Calculate verified transaction amounts
            $verifiedTransactionsAmount = \App\Models\DonationTransaction::where('campaign_id', $campaign->id)
                ->where('status', 'VERIFIED')
                ->sum('amount');

            // Calculate verified donation amounts (old system)
            $verifiedDonationsAmount = \App\Models\Donation::where('campaign_id', $campaign->id)
                ->where('status', 'paid')
                ->sum('amount');

            // Calculate total verified amount
            $totalVerifiedAmount = $verifiedTransactionsAmount + $verifiedDonationsAmount;

            // Update the campaign's current amount
            $campaign->update(['current_amount' => $totalVerifiedAmount]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Campaign amounts recalculated successfully!');
    }
}
