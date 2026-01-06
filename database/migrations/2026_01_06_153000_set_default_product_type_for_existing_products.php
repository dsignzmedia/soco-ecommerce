<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all products with NULL or empty product_type to 'merchandised'
        // This ensures existing products show up on the frontend
        DB::table('product_mappings')
            ->where(function($query) {
                $query->whereNull('product_type')
                      ->orWhere('product_type', '');
            })
            ->update(['product_type' => 'merchandised']);
        
        echo "Updated products with NULL/empty product_type to 'merchandised'\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally revert back to NULL if needed
        // Be careful with this - only use if absolutely necessary
        // DB::table('product_mappings')
        //     ->where('product_type', 'merchandised')
        //     ->update(['product_type' => null]);
    }
};
