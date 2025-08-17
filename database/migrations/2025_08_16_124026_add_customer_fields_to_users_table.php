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
            $table->string('phone')->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->string('address_line_1')->nullable()->after('date_of_birth');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('city')->nullable()->after('address_line_2');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->default('US')->after('postal_code');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('country');
            $table->text('bio')->nullable()->after('gender');
            $table->string('profile_photo')->nullable()->after('bio');
            $table->boolean('marketing_emails')->default(true)->after('profile_photo');
            $table->boolean('order_updates')->default(true)->after('marketing_emails');
            $table->timestamp('last_login_at')->nullable()->after('order_updates');
            $table->string('preferred_sports')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'date_of_birth',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'postal_code',
                'country',
                'gender',
                'bio',
                'profile_photo',
                'marketing_emails',
                'order_updates',
                'last_login_at',
                'preferred_sports'
            ]);
        });
    }
};
