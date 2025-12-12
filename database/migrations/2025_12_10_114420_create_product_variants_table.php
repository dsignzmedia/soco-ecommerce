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
            $table->foreignId('product_mapping_id')->constrained('product_mappings')->cascadeOnDelete();
            $table->string('name')->default('Size'); // e.g. "Size"
            $table->string('option'); // e.g. "S", "M", "10"
            $table->integer('stock')->default(0);
            $table->string('sku')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
