<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 191)->unique();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->date('order_date')->nullable();
            $table->string('student_name')->nullable();
            $table->string('grade')->nullable();
            $table->string('category')->nullable();
            $table->string('item_name')->nullable();
            $table->string('size')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('customer_name');
            $table->text('customer_address')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->string('payment_status')->default('pending');
            $table->string('order_status')->default('processing');
            $table->string('return_exchange_status')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

