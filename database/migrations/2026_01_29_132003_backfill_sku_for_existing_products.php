<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Admin\Master\ProductMapping;
use App\Models\Admin\Master\School;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Backfill SKUs for all existing products that don't have one.
     */
    public function up(): void
    {
        // Get all products without SKU, ordered by creation date
        $products = ProductMapping::withoutGlobalScopes()
            ->whereNull('sku')
            ->orderBy('created_at')
            ->get();

        foreach ($products as $product) {
            try {
                $sku = ProductMapping::generateSku($product);
                $product->update(['sku' => $sku]);
            } catch (\Exception $e) {
                // Log error but continue with next product
                \Log::error("Failed to generate SKU for product ID {$product->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally clear SKUs if needed
        // ProductMapping::withoutGlobalScopes()->update(['sku' => null]);
    }
};
