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
        Schema::table('app_branding', function (Blueprint $table) {
            $table->boolean('maintenance_bts')->default(false)->after('accent_color');
            $table->boolean('maintenance_merch')->default(false)->after('maintenance_bts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_branding', function (Blueprint $table) {
            $table->dropColumn(['maintenance_bts', 'maintenance_merch']);
        });
    }
};
