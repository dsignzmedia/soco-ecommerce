<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add student_name, school_name, and grade snapshot columns to carts so that
     * even if the parent deletes the student profile before/after checkout,
     * the order can still be recorded with the correct student identity.
     */
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->string('student_name')->nullable()->after('profile_id');
            $table->string('school_name')->nullable()->after('student_name');
            $table->string('grade')->nullable()->after('school_name');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['student_name', 'school_name', 'grade']);
        });
    }
};
