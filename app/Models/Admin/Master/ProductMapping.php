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
        'sku',
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



        // Auto-generate SKU when creating a new product
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = static::generateSku($product);
            }
        });
    }

    /**
     * Generate a unique SKU for the product
     * Format: PREFIX + 3-digit sequential number (e.g., BVB001, BTS001, MER001)
     * 
     * @param ProductMapping $product
     * @return string
     */
    public static function generateSku($product): string
    {
        $prefix = '';
        
        // Determine prefix based on product type or school
        if ($product->product_type === 'back_to_school') {
            $prefix = 'BTS';
        } elseif ($product->product_type === 'merchandised') {
            $prefix = 'MER';
        } elseif ($product->school_id) {
            // Get school name and extract first 3 letters
            $school = School::withoutGlobalScopes()->find($product->school_id);
            if ($school && $school->name) {
                $schoolName = $school->name;
                // Remove special characters and spaces, convert to uppercase
                $cleanedName = preg_replace('/[^A-Za-z0-9\s]/', '', $schoolName);
                $words = explode(' ', trim($cleanedName));
                
                // Extract first 3 letters from school name
                if (count($words) >= 3) {
                    // Take first letter of first 3 words
                    $prefix = strtoupper(
                        substr($words[0], 0, 1) . 
                        substr($words[1] ?? '', 0, 1) . 
                        substr($words[2] ?? '', 0, 1)
                    );
                } elseif (count($words) >= 2) {
                    // Take first 2 letters from first word, 1 from second
                    $prefix = strtoupper(
                        substr($words[0], 0, 2) . 
                        substr($words[1] ?? '', 0, 1)
                    );
                } else {
                    // Take first 3 letters from single word
                    $prefix = strtoupper(substr($words[0], 0, 3));
                }
                
                // Ensure prefix is exactly 3 characters
                $prefix = str_pad(substr($prefix, 0, 3), 3, 'X', STR_PAD_RIGHT);
            } else {
                $prefix = 'SCH'; // Fallback for school products without school name
            }
        } else {
            $prefix = 'GLO'; // Global products
        }

        // Find the next available number for this prefix globally
        // This ensures uniqueness even if multiple schools have the same name
        $query = static::withoutGlobalScopes()
            ->where('sku', 'like', $prefix . '%');
        
        $maxSku = $query->max('sku');
        
        if ($maxSku) {
            // Extract number part (after prefix)
            $numberPart = substr($maxSku, strlen($prefix));
            $nextNumber = (int)$numberPart + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Format as 3-digit number
        $sku = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        // Double-check uniqueness (handle edge cases)
        $attempts = 0;
        while (static::withoutGlobalScopes()->where('sku', $sku)->exists() && $attempts < 100) {
            $nextNumber++;
            $sku = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $attempts++;
        }
        
        return $sku;
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

