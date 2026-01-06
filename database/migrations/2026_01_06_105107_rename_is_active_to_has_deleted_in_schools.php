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
        Schema::table('schools', function (Blueprint $table) {
            // First, invert the existing values (1 becomes 0, 0 becomes 1)
            DB::statement('UPDATE schools SET is_active = CASE WHEN is_active = 1 THEN 0 WHEN is_active = 0 THEN 1 ELSE is_active END');
            
            // Then rename the column
            $table->renameColumn('is_active', 'has_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Rename back
            $table->renameColumn('has_deleted', 'is_active');
            
            // Invert values back to original
            DB::statement('UPDATE schools SET is_active = CASE WHEN is_active = 1 THEN 0 WHEN is_active = 0 THEN 1 ELSE is_active END');
        });
    }
};
