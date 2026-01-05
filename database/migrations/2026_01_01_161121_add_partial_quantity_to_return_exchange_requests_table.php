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
        Schema::table('return_exchange_requests', function (Blueprint $table) {
            $table->unsignedInteger('requested_quantity')->default(1)->after('order_id');
            $table->unsignedInteger('returned_quantity')->default(0)->nullable()->after('requested_quantity');
            $table->index(['order_id', 'status']); // For faster lookups
        });
        
        // Update existing records: set requested_quantity = order.quantity for existing requests
        DB::statement("
            UPDATE return_exchange_requests rer
            INNER JOIN orders o ON rer.order_id = o.id
            SET rer.requested_quantity = o.quantity
            WHERE rer.requested_quantity IS NULL OR rer.requested_quantity = 0
        ");
        
        // Set returned_quantity for completed/restocked requests
        DB::statement("
            UPDATE return_exchange_requests rer
            INNER JOIN orders o ON rer.order_id = o.id
            SET rer.returned_quantity = rer.requested_quantity
            WHERE rer.status IN ('received_restocked', 'received_discarded', 'completed')
            AND (rer.returned_quantity IS NULL OR rer.returned_quantity = 0)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_exchange_requests', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'status']);
            $table->dropColumn(['requested_quantity', 'returned_quantity']);
        });
    }
};
