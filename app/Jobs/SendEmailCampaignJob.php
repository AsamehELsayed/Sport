<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Models\EmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    protected $campaign;
    protected $recipient;

    /**
     * Create a new job instance.
     */
    public function __construct(EmailCampaign $campaign, EmailCampaignRecipient $recipient)
    {
        $this->campaign = $campaign;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $emailSetting = EmailSetting::where('user_id', $this->campaign->user_id)
                ->where('is_active', true)
                ->first();

            if (!$emailSetting) {
                Log::error('No active email settings found for campaign', [
                    'campaign_id' => $this->campaign->id,
                    'user_id' => $this->campaign->user_id,
                ]);
                $this->fail(new \Exception('No active email settings found'));
                return;
            }

            // Check if we're within sending schedule
            if (!$emailSetting->isWithinSendingSchedule()) {
                Log::info('Outside sending schedule, delaying email', [
                    'campaign_id' => $this->campaign->id,
                    'recipient_id' => $this->recipient->id,
                ]);
                $this->release(3600); // Release back to queue for 1 hour
                return;
            }

            // Configure mail settings
            $mailConfig = $emailSetting->getMailConfig();
            config([
                'mail.mailers.smtp.host' => $mailConfig['host'],
                'mail.mailers.smtp.port' => $mailConfig['port'],
                'mail.mailers.smtp.username' => $mailConfig['username'],
                'mail.mailers.smtp.password' => $mailConfig['password'],
                'mail.mailers.smtp.encryption' => $mailConfig['encryption'],
                'mail.from.address' => $mailConfig['from']['address'],
                'mail.from.name' => $mailConfig['from']['name'],
            ]);

            // Prepare email content
            $content = $this->prepareEmailContent();
            $subject = $this->prepareEmailSubject();

            // Send email
            Mail::html($content, function ($message) use ($subject) {
                $message->to($this->recipient->recipient->email, $this->recipient->recipient->name)
                    ->subject($subject);

                // Add tracking headers if enabled
                if ($this->campaign->user->emailSettings->first()?->track_opens) {
                    $message->getHeaders()->addTextHeader('X-Campaign-ID', $this->campaign->id);
                    $message->getHeaders()->addTextHeader('X-Recipient-ID', $this->recipient->id);
                    $message->getHeaders()->addTextHeader('X-Tracking-ID', $this->recipient->tracking_id);
                }
            });

            // Update recipient status
            $this->recipient->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // Update campaign stats
            $this->campaign->increment('sent_count');

            Log::info('Email sent successfully', [
                'campaign_id' => $this->campaign->id,
                'recipient_id' => $this->recipient->id,
                'email' => $this->recipient->recipient->email,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send email campaign', [
                'campaign_id' => $this->campaign->id,
                'recipient_id' => $this->recipient->id,
                'error' => $e->getMessage(),
            ]);

            $this->recipient->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function prepareEmailContent(): string
    {
        $content = $this->campaign->content;

        // Replace variables with recipient data
        $variables = [
            'customer_name' => $this->recipient->recipient->name,
            'customer_email' => $this->recipient->recipient->email,
            'customer_phone' => $this->recipient->recipient->phone,
            'website_name' => $this->campaign->user->website_name,
            'unsubscribe_link' => route('unsubscribe', ['token' => $this->recipient->tracking_id]),
        ];

        foreach ($variables as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        // Add tracking pixel if enabled
        if ($this->campaign->user->emailSettings->first()?->track_opens) {
            $trackingPixel = '<img src="' . route('track.open', ['id' => $this->recipient->tracking_id]) . '" width="1" height="1" style="display:none;" />';
            $content .= $trackingPixel;
        }

        return $content;
    }

    protected function prepareEmailSubject(): string
    {
        $subject = $this->campaign->subject;

        // Replace variables in subject
        $variables = [
            'customer_name' => $this->recipient->recipient->name,
            'website_name' => $this->campaign->user->website_name,
        ];

        foreach ($variables as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
        }

        return $subject;
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Email campaign job failed', [
            'campaign_id' => $this->campaign->id,
            'recipient_id' => $this->recipient->id,
            'error' => $exception->getMessage(),
        ]);

        $this->recipient->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
