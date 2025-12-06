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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('student_profiles')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->string('size');
            $table->integer('quantity')->default(1);
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['user_id', 'profile_id', 'product_id', 'size']);
            
            // Prevent duplicate entries for same product/size/profile combination
            $table->unique(['user_id', 'profile_id', 'product_id', 'size'], 'cart_unique_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
