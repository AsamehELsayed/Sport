<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailSettingController extends Controller
{
    public function index(): Response
    {
        $emailSetting = EmailSetting::where('user_id', auth()->id())->first();

        return Inertia::render('admin/email-settings/Index', [
            'emailSetting' => $emailSetting,
            'timezones' => $this->getTimezones(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mail_driver' => 'required|string|in:smtp,mailgun,ses,postmark',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|integer',
            'mail_username' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_password' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_encryption' => 'required_if:mail_driver,smtp|nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'sending_schedule' => 'nullable|array',
            'daily_send_limit' => 'required|integer|min:1|max:10000',
            'track_opens' => 'boolean',
            'track_clicks' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();

        EmailSetting::updateOrCreate(
            ['user_id' => auth()->id()],
            $validated
        );

        return back()->with('success', 'Email settings saved successfully.');
    }

    public function test(Request $request)
    {
        $validated = $request->validate([
            'mail_driver' => 'required|string|in:smtp,mailgun,ses,postmark',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|integer',
            'mail_username' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_password' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_encryption' => 'required_if:mail_driver,smtp|nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        try {
            // Configure mail settings temporarily
            config([
                'mail.mailers.smtp.host' => $validated['mail_host'],
                'mail.mailers.smtp.port' => $validated['mail_port'],
                'mail.mailers.smtp.username' => $validated['mail_username'],
                'mail.mailers.smtp.password' => $validated['mail_password'],
                'mail.mailers.smtp.encryption' => $validated['mail_encryption'],
                'mail.from.address' => $validated['mail_from_address'],
                'mail.from.name' => $validated['mail_from_name'],
            ]);

            // Send test email
            \Mail::raw('This is a test email from your SportApp email settings.', function ($message) use ($validated) {
                $message->to($validated['mail_from_address'])
                    ->subject('Test Email - SportApp Email Settings');
            });

            return response()->json(['success' => true, 'message' => 'Test email sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send test email: ' . $e->getMessage()], 422);
        }
    }

    private function getTimezones(): array
    {
        return [
            'UTC' => 'UTC',
            'America/New_York' => 'Eastern Time (US & Canada)',
            'America/Chicago' => 'Central Time (US & Canada)',
            'America/Denver' => 'Mountain Time (US & Canada)',
            'America/Los_Angeles' => 'Pacific Time (US & Canada)',
            'Europe/London' => 'London',
            'Europe/Paris' => 'Paris',
            'Europe/Berlin' => 'Berlin',
            'Asia/Tokyo' => 'Tokyo',
            'Asia/Shanghai' => 'Shanghai',
            'Asia/Dubai' => 'Dubai',
            'Australia/Sydney' => 'Sydney',
        ];
    }
}
