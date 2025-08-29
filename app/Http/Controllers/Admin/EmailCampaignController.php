<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\CustomerGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailCampaignController extends Controller
{
    public function index(): Response
    {
        $campaigns = EmailCampaign::where('user_id', auth()->id())
            ->with(['template'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('admin/email-campaigns/Index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function create(): Response
    {
        $templates = EmailTemplate::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();

        $customerGroups = CustomerGroup::where('is_active', true)->get();

        return Inertia::render('admin/email-campaigns/Create', [
            'templates' => $templates,
            'customerGroups' => $customerGroups,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'status' => 'required|in:draft,scheduled,sending,sent,cancelled',
            'scheduled_at' => 'nullable|date|after:now',
            'recipient_filters' => 'nullable|array',
            'customer_group_ids' => 'nullable|array',
            'customer_group_ids.*' => 'exists:customer_groups,id',
        ]);

        $validated['user_id'] = auth()->id();

        $campaign = EmailCampaign::create($validated);

        if (!empty($validated['customer_group_ids'])) {
            $campaign->customerGroups()->attach($validated['customer_group_ids']);
        }

        return redirect()->route('admin.email-campaigns.index')
            ->with('success', 'Email campaign created successfully.');
    }

    public function show(EmailCampaign $emailCampaign): Response
    {
        $this->authorize('view', $emailCampaign);

        $emailCampaign->load(['template', 'recipients.recipient', 'customerGroups']);

        return Inertia::render('admin/email-campaigns/Show', [
            'campaign' => $emailCampaign,
        ]);
    }

    public function edit(EmailCampaign $emailCampaign): Response
    {
        $this->authorize('update', $emailCampaign);

        $templates = EmailTemplate::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();

        $customerGroups = CustomerGroup::where('is_active', true)->get();

        $emailCampaign->load('customerGroups');

        return Inertia::render('admin/email-campaigns/Edit', [
            'campaign' => $emailCampaign,
            'templates' => $templates,
            'customerGroups' => $customerGroups,
        ]);
    }

    public function update(Request $request, EmailCampaign $emailCampaign)
    {
        $this->authorize('update', $emailCampaign);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'status' => 'required|in:draft,scheduled,sending,sent,cancelled',
            'scheduled_at' => 'nullable|date|after:now',
            'recipient_filters' => 'nullable|array',
            'customer_group_ids' => 'nullable|array',
            'customer_group_ids.*' => 'exists:customer_groups,id',
        ]);

        $emailCampaign->update($validated);

        if (isset($validated['customer_group_ids'])) {
            $emailCampaign->customerGroups()->sync($validated['customer_group_ids']);
        }

        return redirect()->route('admin.email-campaigns.index')
            ->with('success', 'Email campaign updated successfully.');
    }

    public function destroy(EmailCampaign $emailCampaign)
    {
        $this->authorize('delete', $emailCampaign);

        $emailCampaign->delete();

        return redirect()->route('admin.email-campaigns.index')
            ->with('success', 'Email campaign deleted successfully.');
    }

    public function send(EmailCampaign $emailCampaign)
    {
        $this->authorize('update', $emailCampaign);

        if ($emailCampaign->status !== 'draft') {
            return back()->with('error', 'Only draft campaigns can be sent.');
        }

        // Get recipients based on customer groups
        $recipients = collect();

        if ($emailCampaign->customerGroups->isNotEmpty()) {
            foreach ($emailCampaign->customerGroups as $group) {
                $recipients = $recipients->merge($group->customers);
            }
        }

        // Remove duplicates and filter by marketing emails preference
        $recipients = $recipients->unique('id')
            ->filter(function ($customer) {
                return $customer->marketing_emails;
            });

        // Create recipient records
        foreach ($recipients as $recipient) {
            $emailCampaign->recipients()->create([
                'recipient_id' => $recipient->id,
                'status' => 'pending',
                'tracking_id' => uniqid('track_', true),
            ]);
        }

        $emailCampaign->update([
            'status' => 'sending',
            'total_recipients' => $recipients->count(),
        ]);

        // Dispatch job to send emails
        // SendEmailCampaignJob::dispatch($emailCampaign);

        return back()->with('success', 'Campaign is being sent to ' . $recipients->count() . ' recipients.');
    }

    public function preview(EmailCampaign $emailCampaign)
    {
        $this->authorize('view', $emailCampaign);

        // Sample data for preview
        $sampleData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'order_number' => 'ORD-12345',
            'order_total' => '$99.99',
            'website_name' => auth()->user()->website_name ?? 'SportApp',
        ];

        $previewContent = $emailCampaign->content;
        if ($emailCampaign->template) {
            $previewContent = $emailCampaign->template->replaceVariables($previewContent, $sampleData);
        }

        return response()->json([
            'subject' => $emailCampaign->subject,
            'content' => $previewContent,
        ]);
    }

    public function getRecipients(EmailCampaign $emailCampaign)
    {
        $this->authorize('view', $emailCampaign);

        $recipients = $emailCampaign->recipients()
            ->with('recipient')
            ->paginate(20);

        return response()->json($recipients);
    }
}
