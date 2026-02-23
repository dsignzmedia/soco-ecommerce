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
        Schema::create('product_mapping_school', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_mapping_id');
            $table->unsignedBigInteger('school_id');
            $table->timestamps();

            $table->foreign('product_mapping_id')->references('id')->on('product_mappings')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            
            // Prevent duplicate entries
            $table->unique(['product_mapping_id', 'school_id']);
        });

        // Migrate existing data
        $products = \DB::table('product_mappings')->whereNotNull('school_id')->get();
        foreach ($products as $product) {
            \DB::table('product_mapping_school')->insert([
                'product_mapping_id' => $product->id,
                'school_id' => $product->school_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_mapping_school');
    }
};
