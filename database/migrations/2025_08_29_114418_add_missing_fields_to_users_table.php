<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add fields that don't already exist
            // Note: phone, date_of_birth, address fields, gender, bio, profile_photo,
            // marketing_emails, order_updates, last_login_at already exist from previous migration

            // Change preferred_sports from string to json
            $table->json('preferred_sports')->nullable()->change();

            // Add missing account settings fields
            $table->string('timezone')->nullable()->after('preferred_sports');
            $table->string('page_name')->nullable()->after('timezone');
            $table->string('website_name')->nullable()->after('page_name');
            $table->string('logo_path')->nullable()->after('website_name');
            $table->string('primary_color', 7)->default('#3B82F6')->after('logo_path');
            $table->string('secondary_color', 7)->default('#1F2937')->after('primary_color');
            $table->string('accent_color', 7)->default('#10B981')->after('secondary_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Change preferred_sports back to string
            $table->string('preferred_sports')->nullable()->change();

            // Drop the new fields
            $table->dropColumn([
                'timezone',
                'page_name',
                'website_name',
                'logo_path',
                'primary_color',
                'secondary_color',
                'accent_color',
            ]);
        });
    }
};
