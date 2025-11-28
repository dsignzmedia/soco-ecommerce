<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grade_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('category')->nullable();
            $table->string('product_type')->nullable();
            $table->string('gender')->default('unisex');
            $table->decimal('price_regular', 10, 2)->nullable();
            $table->decimal('price_sale', 10, 2)->nullable();
            $table->unsignedInteger('inventory_stock')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(0);
            $table->string('status')->default('live');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_mappings');
    }
};

