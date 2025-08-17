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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('original_price', 10, 2)->after('price')->default(0);
            $table->decimal('discount_amount', 10, 2)->after('original_price')->default(0);
            $table->decimal('discount_percentage', 5, 2)->after('discount_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'discount_amount', 'discount_percentage']);
        });
    }
};
