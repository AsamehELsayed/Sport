<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WebsiteSetting;

class WebsiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Website Info
            [
                'key' => 'website_name',
                'value' => 'SportApp',
                'type' => 'string',
                'group' => 'website',
                'label' => 'Website Name',
                'description' => 'The name of your website',
            ],
            [
                'key' => 'page_name',
                'value' => 'Home',
                'type' => 'string',
                'group' => 'website',
                'label' => 'Page Name',
                'description' => 'The name of your main page',
            ],
            [
                'key' => 'timezone',
                'value' => 'UTC',
                'type' => 'string',
                'group' => 'website',
                'label' => 'Timezone',
                'description' => 'Default timezone for the website',
            ],
            [
                'key' => 'logo_path',
                'value' => null,
                'type' => 'image',
                'group' => 'website',
                'label' => 'Logo',
                'description' => 'Website logo image',
            ],

            // Appearance
            [
                'key' => 'primary_color',
                'value' => '#3B82F6',
                'type' => 'string',
                'group' => 'appearance',
                'label' => 'Primary Color',
                'description' => 'Primary brand color',
            ],
            [
                'key' => 'secondary_color',
                'value' => '#1F2937',
                'type' => 'string',
                'group' => 'appearance',
                'label' => 'Secondary Color',
                'description' => 'Secondary brand color',
            ],
            [
                'key' => 'accent_color',
                'value' => '#10B981',
                'type' => 'string',
                'group' => 'appearance',
                'label' => 'Accent Color',
                'description' => 'Accent brand color',
            ],

            // Hero Section
            [
                'key' => 'hero_background_image',
                'value' => null,
                'type' => 'image',
                'group' => 'hero',
                'label' => 'Hero Background Image',
                'description' => 'Background image for the hero section',
            ],
            [
                'key' => 'hero_badge_text',
                'value' => '🔥 New Collection Available',
                'type' => 'string',
                'group' => 'hero',
                'label' => 'Hero Badge Text',
                'description' => 'Text displayed in the hero badge',
            ],
            [
                'key' => 'hero_title_line1',
                'value' => 'Unleash Your',
                'type' => 'string',
                'group' => 'hero',
                'label' => 'Hero Title Line 1',
                'description' => 'First line of the hero title',
            ],
            [
                'key' => 'hero_title_line2',
                'value' => 'Athletic Power',
                'type' => 'string',
                'group' => 'hero',
                'label' => 'Hero Title Line 2',
                'description' => 'Second line of the hero title',
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'Discover premium sports equipment designed for champions. From professional-grade gear to everyday athletic essentials, elevate your performance with our cutting-edge collection.',
                'type' => 'text',
                'group' => 'hero',
                'label' => 'Hero Subtitle',
                'description' => 'Subtitle text below the hero title',
            ],
            [
                'key' => 'hero_cta_primary_text',
                'value' => 'Shop Collection',
                'type' => 'string',
                'group' => 'hero',
                'label' => 'Primary CTA Button Text',
                'description' => 'Text for the primary call-to-action button',
            ],
            [
                'key' => 'hero_cta_primary_url',
                'value' => '/categories',
                'type' => 'string',
                'group' => 'hero',
                'label' => 'Primary CTA Button URL',
                'description' => 'URL for the primary call-to-action button',
            ],
            [
                'key' => 'hero_cta_secondary_text',
                'value' => 'Watch Stories',
                'type' => 'string',
                'group' => 'hero',
                'label' => 'Secondary CTA Button Text',
                'description' => 'Text for the secondary call-to-action button',
            ],
            [
                'key' => 'hero_cta_secondary_url',
                'value' => null,
                'type' => 'string',
                'group' => 'hero',
                'label' => 'Secondary CTA Button URL',
                'description' => 'URL for the secondary call-to-action button',
            ],
        ];

        foreach ($settings as $setting) {
            WebsiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
