<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_mapping_id',
        'name',
        'option',
        'stock',
        'low_stock_threshold'
    ];

    public function product()
    {
        return $this->belongsTo(ProductMapping::class, 'product_mapping_id');
    }
}
