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
            $table->string('signature_path')->nullable()->after('seller_pan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_branding', function (Blueprint $table) {
            $table->dropColumn('signature_path');
        });
    }
};
