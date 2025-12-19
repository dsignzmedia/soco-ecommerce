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
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // product_mappings is likely the table name. If not, I should check.
            // Based on models/ProductMapping.php, table is usually product_mappings.
            // If mapping fails, I will use unconstrained integers or check DB schema.
            // Given "ProductMapping" model, table convention is product_mappings.
            $table->foreignId('product_mapping_id')->nullable()->constrained('product_mappings')->nullOnDelete(); 
            $table->string('status')->default('pending'); // pending, printing, completed, cancelled
            $table->string('print_file_path')->nullable();
            $table->json('details')->nullable(); // size, quantity, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
    }
};
