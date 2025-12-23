<?php

namespace App\Models;

use App\Models\Admin\Master\ProductMapping;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductGradePricing extends Model
{
    protected $table = 'product_grade_pricing';

    protected $fillable = [
        'product_mapping_id',
        'grade',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the product mapping that owns this grade pricing.
     */
    public function productMapping(): BelongsTo
    {
        return $this->belongsTo(ProductMapping::class, 'product_mapping_id');
    }
}
