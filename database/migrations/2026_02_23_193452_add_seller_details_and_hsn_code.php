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
            $table->string('seller_name')->nullable()->after('app_name');
            $table->text('seller_address')->nullable()->after('seller_name');
            $table->string('seller_gstin')->nullable()->after('seller_address');
            $table->string('seller_fssai')->nullable()->after('seller_gstin');
            $table->string('seller_cin')->nullable()->after('seller_fssai');
            $table->string('seller_pan')->nullable()->after('seller_cin');
        });

        Schema::table('product_mappings', function (Blueprint $table) {
            $table->string('hsn_code')->nullable()->after('product_name');
        });
    }

    public function down(): void
    {
        Schema::table('app_branding', function (Blueprint $table) {
            $table->dropColumn([
                'seller_name',
                'seller_address',
                'seller_gstin',
                'seller_fssai',
                'seller_cin',
                'seller_pan'
            ]);
        });

        Schema::table('product_mappings', function (Blueprint $table) {
            $table->dropColumn('hsn_code');
        });
    }
};
