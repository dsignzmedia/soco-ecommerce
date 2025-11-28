<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_mapping_id',
        'quantity_change',
        'reason',
        'comment',
        'stock_before',
        'stock_after',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductMapping::class, 'product_mapping_id');
    }
}

