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
        Schema::table('product_mappings', function (Blueprint $table) {
            $table->string('gender')->nullable()->change();
            // school_id was arguably made nullable in a previous migration (2025_12_18_071639),
            // but we double check here to be sure, or just fix gender.
            // If the user's DB says 'school_id' is already nullable, this is fine.
            // However, the user's error trace showed 'gender' cannot be null, not school_id.
            // Let's ensure both are nullable as requested.
            $table->unsignedBigInteger('school_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_mappings', function (Blueprint $table) {
            $table->string('gender')->default('unisex')->nullable(false)->change();
            // Depending on previous state, you might want to revert school_id too, 
            // but usually safest to leave it nullable in down() or revert to strict if known.
            // For now, we only revert gender.
        });
    }
};
