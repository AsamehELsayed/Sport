<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailCampaignJob;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use Illuminate\Console\Command;

class ProcessEmailCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:process-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending email campaigns and send emails to recipients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting email campaign processing...');

        // Get campaigns that are ready to be sent
        $campaigns = EmailCampaign::where('status', 'sending')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($campaigns as $campaign) {
            $this->info("Processing campaign: {$campaign->name}");

            // Get pending recipients
            $recipients = $campaign->recipients()
                ->where('status', 'pending')
                ->limit(50) // Process in batches
                ->get();

            if ($recipients->isEmpty()) {
                $this->info("No pending recipients for campaign: {$campaign->name}");

                // Check if all emails have been sent
                $totalRecipients = $campaign->recipients()->count();
                $sentRecipients = $campaign->recipients()->where('status', 'sent')->count();

                if ($sentRecipients >= $totalRecipients) {
                    $campaign->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                    $this->info("Campaign completed: {$campaign->name}");
                }

                continue;
            }

            foreach ($recipients as $recipient) {
                // Dispatch job to send email
                SendEmailCampaignJob::dispatch($campaign, $recipient);
                $this->line("Queued email for: {$recipient->recipient->email}");
            }

            $this->info("Queued {$recipients->count()} emails for campaign: {$campaign->name}");
        }

        $this->info('Email campaign processing completed.');
    }
}
