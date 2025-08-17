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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('size')->nullable(); // S, M, L, XL, etc.
            $table->string('color')->nullable(); // Red, Blue, etc.
            $table->string('sku')->unique()->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('price_adjustment', 10, 2)->default(0.00); // For size/color specific pricing
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Composite unique constraint
            $table->unique(['product_id', 'size', 'color'], 'product_variant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
