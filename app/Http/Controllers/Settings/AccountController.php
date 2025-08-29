<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\AccountUpdateRequest;
use App\Models\WebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    /**
     * Show the website settings page.
     */
    public function edit(Request $request): Response
    {
        // Get all website settings
        $websiteSettings = WebsiteSetting::getAllAsArray();

        return Inertia::render('settings/Account', [
            'settings' => [
                'website_name' => $websiteSettings['website_name'] ?? config('app.name'),
                'page_name' => $websiteSettings['page_name'] ?? config('app.name'),
                'timezone' => $websiteSettings['timezone'] ?? config('app.timezone'),
                'logo_path' => $websiteSettings['logo_path'] ?? null,
                'primary_color' => $websiteSettings['primary_color'] ?? '#3B82F6',
                'secondary_color' => $websiteSettings['secondary_color'] ?? '#1F2937',
                'accent_color' => $websiteSettings['accent_color'] ?? '#10B981',
                // Hero section settings
                'hero_background_image' => $websiteSettings['hero_background_image'] ? asset('storage/' . $websiteSettings['hero_background_image']) : null,
                'hero_badge_text' => $websiteSettings['hero_badge_text'] ?? '🔥 New Collection Available',
                'hero_title_line1' => $websiteSettings['hero_title_line1'] ?? 'Unleash Your',
                'hero_title_line2' => $websiteSettings['hero_title_line2'] ?? 'Athletic Power',
                'hero_subtitle' => $websiteSettings['hero_subtitle'] ?? 'Discover premium sports equipment designed for champions. From professional-grade gear to everyday athletic essentials, elevate your performance with our cutting-edge collection.',
                'hero_cta_primary_text' => $websiteSettings['hero_cta_primary_text'] ?? 'Shop Collection',
                'hero_cta_primary_url' => $websiteSettings['hero_cta_primary_url'] ?? '/categories',
                'hero_cta_secondary_text' => $websiteSettings['hero_cta_secondary_text'] ?? 'Watch Stories',
                'hero_cta_secondary_url' => $websiteSettings['hero_cta_secondary_url'] ?? null,
            ],
            'timezones' => $this->getTimezones(),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the website settings.
     */
    public function update(AccountUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            WebsiteSetting::setValue('logo_path', $logoPath);
        }

        // Handle hero background image upload
        if ($request->hasFile('hero_background_image_file')) {
            $heroImagePath = $request->file('hero_background_image_file')->store('hero', 'public');
            WebsiteSetting::setValue('hero_background_image', $heroImagePath);
        }

        // Update website settings
        $settingsToUpdate = [
            'website_name',
            'page_name',
            'timezone',
            'primary_color',
            'secondary_color',
            'accent_color',
            'hero_badge_text',
            'hero_title_line1',
            'hero_title_line2',
            'hero_subtitle',
            'hero_cta_primary_text',
            'hero_cta_primary_url',
            'hero_cta_secondary_text',
            'hero_cta_secondary_url',
        ];

        foreach ($settingsToUpdate as $setting) {
            if (isset($validated[$setting])) {
                WebsiteSetting::setValue($setting, $validated[$setting]);
            }
        }

        // Clear all settings cache
        WebsiteSetting::clearCache();

        return redirect()->route('admin.settings.account.edit')->with('status', 'Website settings updated successfully.');
    }

    /**
     * Get list of available timezones from API.
     */
    private function getTimezones(): array
    {
        // Try to get timezones from API with better error handling
        $timezones = $this->fetchTimezonesFromAPI();

        if (!empty($timezones)) {
            return $timezones;
        }

        // Fallback to comprehensive list of timezones
        return [
            'UTC' => 'UTC',
            'America/New_York' => 'Eastern Time (US & Canada)',
            'America/Chicago' => 'Central Time (US & Canada)',
            'America/Denver' => 'Mountain Time (US & Canada)',
            'America/Los_Angeles' => 'Pacific Time (US & Canada)',
            'America/Anchorage' => 'Alaska',
            'Pacific/Honolulu' => 'Hawaii',
            'Europe/London' => 'London',
            'Europe/Paris' => 'Paris',
            'Europe/Berlin' => 'Berlin',
            'Europe/Moscow' => 'Moscow',
            'Europe/Rome' => 'Rome',
            'Europe/Madrid' => 'Madrid',
            'Europe/Amsterdam' => 'Amsterdam',
            'Europe/Vienna' => 'Vienna',
            'Europe/Zurich' => 'Zurich',
            'Europe/Stockholm' => 'Stockholm',
            'Europe/Oslo' => 'Oslo',
            'Europe/Copenhagen' => 'Copenhagen',
            'Europe/Helsinki' => 'Helsinki',
            'Europe/Warsaw' => 'Warsaw',
            'Europe/Prague' => 'Prague',
            'Europe/Budapest' => 'Budapest',
            'Europe/Athens' => 'Athens',
            'Europe/Istanbul' => 'Istanbul',
            'Asia/Tokyo' => 'Tokyo',
            'Asia/Shanghai' => 'Shanghai',
            'Asia/Seoul' => 'Seoul',
            'Asia/Beijing' => 'Beijing',
            'Asia/Hong_Kong' => 'Hong Kong',
            'Asia/Singapore' => 'Singapore',
            'Asia/Bangkok' => 'Bangkok',
            'Asia/Manila' => 'Manila',
            'Asia/Jakarta' => 'Jakarta',
            'Asia/Kolkata' => 'Kolkata',
            'Asia/Dubai' => 'Dubai',
            'Asia/Tehran' => 'Tehran',
            'Asia/Karachi' => 'Karachi',
            'Asia/Dhaka' => 'Dhaka',
            'Asia/Yangon' => 'Yangon',
            'Australia/Sydney' => 'Sydney',
            'Australia/Melbourne' => 'Melbourne',
            'Australia/Brisbane' => 'Brisbane',
            'Australia/Perth' => 'Perth',
            'Australia/Adelaide' => 'Adelaide',
            'Pacific/Auckland' => 'Auckland',
            'Pacific/Fiji' => 'Fiji',
            'Pacific/Guam' => 'Guam',
            'Pacific/Honolulu' => 'Hawaii',
            'Africa/Cairo' => 'Cairo',
            'Africa/Johannesburg' => 'Johannesburg',
            'Africa/Lagos' => 'Lagos',
            'Africa/Nairobi' => 'Nairobi',
            'Africa/Casablanca' => 'Casablanca',
            'America/Sao_Paulo' => 'Sao Paulo',
            'America/Buenos_Aires' => 'Buenos Aires',
            'America/Santiago' => 'Santiago',
            'America/Lima' => 'Lima',
            'America/Mexico_City' => 'Mexico City',
            'America/Toronto' => 'Toronto',
            'America/Vancouver' => 'Vancouver',
            'America/Montreal' => 'Montreal',
        ];
    }

    /**
     * Fetch timezones from World Time API.
     */
    private function fetchTimezonesFromAPI(): array
    {
        try {
            // Set a timeout for the request
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5, // 5 seconds timeout
                    'user_agent' => 'SportApp/1.0',
                ]
            ]);

            $response = file_get_contents('https://worldtimeapi.org/api/timezone', false, $context);

            if ($response === false) {
                return [];
            }

            $timezones = json_decode($response, true);

            if ($timezones && is_array($timezones)) {
                $formattedTimezones = [];
                foreach ($timezones as $timezone) {
                    $formattedTimezones[$timezone] = $this->formatTimezoneName($timezone);
                }

                // Sort by formatted name
                asort($formattedTimezones);
                return $formattedTimezones;
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::warning('Failed to fetch timezones from API: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Format timezone name for display.
     */
    private function formatTimezoneName(string $timezone): string
    {
        $parts = explode('/', $timezone);
        if (count($parts) === 2) {
            $region = str_replace('_', ' ', $parts[0]);
            $city = str_replace('_', ' ', $parts[1]);
            return "$city ($region)";
        }

        return str_replace('_', ' ', $timezone);
    }
}
