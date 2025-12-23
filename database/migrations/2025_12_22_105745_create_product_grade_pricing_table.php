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
        Schema::create('product_grade_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_mapping_id')->constrained('product_mappings')->onDelete('cascade');
            $table->string('grade', 50); // e.g., 'Pre-KG', 'LKG', 'Class 1', etc.
            $table->decimal('price', 10, 2);
            $table->timestamps();
            
            // Ensure one price per product-grade combination
            $table->unique(['product_mapping_id', 'grade']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_grade_pricing');
    }
};
