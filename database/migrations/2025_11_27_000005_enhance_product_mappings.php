<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_mappings', function (Blueprint $table) {
            $table->string('stock_status')->default('in_stock')->after('gender');
            $table->string('availability_label')->nullable()->after('stock_status');
            $table->string('tax_profile')->nullable()->after('price_tax');
            $table->decimal('product_weight', 8, 2)->nullable()->after('tax_profile');
            $table->string('tag_name')->nullable()->after('product_weight');
            $table->string('featured_image')->nullable()->after('tag_name');
            $table->string('size_measurement_image')->nullable()->after('media_size_chart');
        });
    }

    public function down(): void
    {
        Schema::table('product_mappings', function (Blueprint $table) {
            $table->dropColumn([
                'stock_status',
                'availability_label',
                'tax_profile',
                'product_weight',
                'tag_name',
                'featured_image',
                'size_measurement_image',
            ]);
        });
    }
};

