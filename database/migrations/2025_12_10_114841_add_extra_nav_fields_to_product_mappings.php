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
            $table->string('size_chart_path')->nullable()->after('size_measurement_image');
            $table->string('video_url')->nullable()->after('size_chart_path');
        });
    }

    public function down(): void
    {
        Schema::table('product_mappings', function (Blueprint $table) {
            $table->dropColumn(['size_chart_path', 'video_url']);
        });
    }
};
