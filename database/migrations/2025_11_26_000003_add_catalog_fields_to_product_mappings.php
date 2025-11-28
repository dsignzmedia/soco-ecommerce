<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_mappings', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('status');
            $table->longText('size_guidance')->nullable()->after('description');
            $table->decimal('price_tax', 8, 2)->nullable()->after('price_sale');
            $table->json('media_images')->nullable()->after('low_stock_threshold');
            $table->json('media_gallery')->nullable()->after('media_images');
            $table->string('media_size_chart')->nullable()->after('media_gallery');
            $table->string('media_measurement_video')->nullable()->after('media_size_chart');
        });
    }

    public function down(): void
    {
        Schema::table('product_mappings', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'size_guidance',
                'price_tax',
                'media_images',
                'media_gallery',
                'media_size_chart',
                'media_measurement_video',
            ]);
        });
    }
};

