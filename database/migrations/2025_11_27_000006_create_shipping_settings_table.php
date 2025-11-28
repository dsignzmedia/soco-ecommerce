<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('default_cost', 10, 2)->default(0);
            $table->json('zones')->nullable(); // legacy field retained for compatibility
            $table->json('overrides')->nullable();
            $table->timestamps();
        });

        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('cost', 10, 2)->default(0);
            $table->boolean('free_shipping')->default(false);
            $table->unsignedInteger('free_threshold')->nullable();
            $table->timestamps();
        });

        Schema::create('shipping_zone_pincodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
            $table->string('pincode');
            $table->timestamps();
        });

        Schema::create('tax_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('tax_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_category_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // category or product
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_assignments');
        Schema::dropIfExists('tax_categories');
        Schema::dropIfExists('shipping_zone_pincodes');
        Schema::dropIfExists('shipping_zones');
        Schema::dropIfExists('shipping_settings');
    }
};

