<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
        'delivery_price',
        'delivery_duration',
        'price_tax',
        'tax_profile',
        'price_inclusive_tax',
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
        'video_url',
        'video_file'
    ];

    protected $casts = [
        'media_images' => 'array',
        'media_gallery' => 'array',
    ];

    /**
     * The "booted" method of the model.
     * Add global scope to hide products from deleted schools
     */
    protected static function booted(): void
    {
        static::addGlobalScope('activeSchool', function (Builder $builder) {
            $builder->where(function($q) {
                // Allow products that are not linked to any school (Global/Merchandise)
                $q->whereNull('school_id')
                  // OR products linked to an active school
                  ->orWhereHas('school', function ($query) {
                      // School model's global scope will automatically filter has_deleted = 0
                  });
            });
        });
    }

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

    public function gradePricing(): HasMany
    {
        return $this->hasMany(\App\Models\ProductGradePricing::class, 'product_mapping_id');
    }

    /**
     * Helper to update total inventory stock based on sum of variants.
     * Only updates if variants exist, preserving manual stock for products without variants.
     */
    public function updateTotalStock()
    {
        // Refresh variants relationship to get latest data
        $this->load('variants');
        
        if ($this->variants && $this->variants->count() > 0) {
            $total = $this->variants->sum('stock');
            $this->update(['inventory_stock' => $total]);
        }
        // If no variants exist, don't modify inventory_stock (preserves manual entry)
    }

    /**
     * Get the display price for the product.
     * Checks price_regular first, then grade pricing, then variant pricing.
     * 
     * @return float
     */
    public function getDisplayPrice(): float
    {
        // First, check if there's a direct price_regular value
        if ($this->price_regular && $this->price_regular > 0) {
            return (float) $this->price_regular;
        }

        // Load grade pricing if not already loaded
        if (!$this->relationLoaded('gradePricing')) {
            $this->load('gradePricing');
        }

        // Check if there's grade-based pricing
        if ($this->gradePricing && $this->gradePricing->count() > 0) {
            $prices = $this->gradePricing->pluck('price')->filter(function($price) {
                return $price !== null && $price > 0;
            });
            
            if ($prices->count() > 0) {
                // Return the minimum price from grade pricing
                return (float) $prices->min();
            }
        }

        // Load variants if not already loaded
        if (!$this->relationLoaded('variants')) {
            $this->load('variants');
        }

        // Check if there are variants with prices
        if ($this->variants && $this->variants->count() > 0) {
            $prices = $this->variants->pluck('price')->filter(function($price) {
                return $price !== null && $price > 0;
            });
            
            if ($prices->count() > 0) {
                // Return the minimum price from variants
                return (float) $prices->min();
            }
        }

        // Fallback to 0 if no price found
        return 0.0;
    }
}

