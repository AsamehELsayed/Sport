<?php

namespace App\Console\Commands;

use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupOldEmailCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:cleanup-old-campaigns {--days=90 : Number of days to keep campaign data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old email campaign data to maintain database performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Cleaning up email campaign data older than {$days} days...");

        // Count records to be deleted
        $oldCampaigns = EmailCampaign::where('created_at', '<', $cutoffDate)->count();
        $oldRecipients = EmailCampaignRecipient::where('created_at', '<', $cutoffDate)->count();

        if ($oldCampaigns === 0 && $oldRecipients === 0) {
            $this->info('No old campaign data found to clean up.');
            return;
        }

        // Delete old recipient records first (due to foreign key constraints)
        $deletedRecipients = EmailCampaignRecipient::where('created_at', '<', $cutoffDate)->delete();
        $this->info("Deleted {$deletedRecipients} old recipient records.");

        // Delete old campaigns
        $deletedCampaigns = EmailCampaign::where('created_at', '<', $cutoffDate)->delete();
        $this->info("Deleted {$deletedCampaigns} old campaign records.");

        $this->info('Cleanup completed successfully!');
    }
}
