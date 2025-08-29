<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailTemplateController extends Controller
{
    public function index(): Response
    {
        $templates = EmailTemplate::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('admin/email-templates/Index', [
            'templates' => $templates,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/email-templates/Create', [
            'availableVariables' => (new EmailTemplate())->getAvailableVariables(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:custom,sales,newsletter,welcome,abandoned_cart',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();

        EmailTemplate::create($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    public function show(EmailTemplate $emailTemplate): Response
    {
        $this->authorize('view', $emailTemplate);

        return Inertia::render('admin/email-templates/Show', [
            'template' => $emailTemplate,
            'availableVariables' => $emailTemplate->getAvailableVariables(),
        ]);
    }

    public function edit(EmailTemplate $emailTemplate): Response
    {
        $this->authorize('update', $emailTemplate);

        return Inertia::render('admin/email-templates/Edit', [
            'template' => $emailTemplate,
            'availableVariables' => $emailTemplate->getAvailableVariables(),
        ]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $this->authorize('update', $emailTemplate);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:custom,sales,newsletter,welcome,abandoned_cart',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $emailTemplate->update($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $this->authorize('delete', $emailTemplate);

        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    public function preview(EmailTemplate $emailTemplate)
    {
        $this->authorize('view', $emailTemplate);

        // Sample data for preview
        $sampleData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'order_number' => 'ORD-12345',
            'order_total' => '$99.99',
            'website_name' => auth()->user()->website_name ?? 'SportApp',
        ];

        $previewContent = $emailTemplate->replaceVariables($emailTemplate->content, $sampleData);

        return response()->json([
            'subject' => $emailTemplate->replaceVariables($emailTemplate->subject, $sampleData),
            'content' => $previewContent,
        ]);
    }
}
