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
        Schema::create('return_exchange_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('type'); // return, exchange
            $table->string('status')->default('pending'); // pending, approved, rejected, received_restocked, received_discarded, completed
            $table->string('reason')->nullable();
            $table->text('admin_notes')->nullable();
            
            // For Exchange
            $table->string('exchange_product_name')->nullable();
            $table->string('exchange_size')->nullable();
            $table->unsignedBigInteger('new_order_id')->nullable(); // For the generated exchange order
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_exchange_requests');
    }
};
