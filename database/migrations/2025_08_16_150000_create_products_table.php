<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->json('images')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->string('sku')->unique()->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->tinyInteger('discount')->unsigned()->default(0); // 0-100
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
