<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'grade_id',
        'grade',
        'product_name',
        'category',
        'product_type',
        'gender',
        'stock_status',
        'availability_label',
        'price_regular',
        'price_sale',
        'price_tax',
        'tax_profile',
        'product_weight',
        'tag_name',
        'featured_image',
        'inventory_stock',
        'low_stock_threshold',
        'status',
        'description',
        'size_guidance',
        'media_images',
        'media_gallery',
        'media_size_chart',
        'size_measurement_image',
        'media_measurement_video',
        'size_chart_path',
        'video_url'
    ];

    protected $casts = [
        'media_images' => 'array',
        'media_gallery' => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function inventoryAdjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class, 'product_mapping_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(\App\Models\ProductVariant::class, 'product_mapping_id');
    }

    /**
     * Helper to update total inventory stock based on sum of variants.
     */
    public function updateTotalStock()
    {
        if ($this->variants()->exists()) {
            $total = $this->variants()->sum('stock');
            $this->update(['inventory_stock' => $total]);
        }
    }
}

