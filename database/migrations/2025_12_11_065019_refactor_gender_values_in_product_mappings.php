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
        // Update product_mappings table
        \Illuminate\Support\Facades\DB::table('product_mappings')
            ->where('gender', 'boys')
            ->update(['gender' => 'male']);
            
        \Illuminate\Support\Facades\DB::table('product_mappings')
            ->where('gender', 'girls')
            ->update(['gender' => 'female']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('product_mappings')
            ->where('gender', 'male')
            ->update(['gender' => 'boys']);
            
        \Illuminate\Support\Facades\DB::table('product_mappings')
            ->where('gender', 'female')
            ->update(['gender' => 'girls']);
    }
};
