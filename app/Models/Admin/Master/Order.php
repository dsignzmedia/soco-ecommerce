<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'school_id',
        'order_date',
        'student_name',
        'grade',
        'category',
        'product_type',
        'item_name',
        'size',
        'quantity',
        'customer_name',
        'customer_address',
        'customer_phone',
        'customer_email',
        'total_amount',
        'tax_amount',
        'shipping_cost',
        'payment_status',
        'order_status',
        'return_exchange_status',
        'tracking_number',
        'courier_name',
        'notes',
        'payment_method',
        'payment_id',
        'amount_paid',
        'payment_details',
        'coupon_id',
    ];

    /**
     * Get the coupon associated with the order.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    protected $casts = [
        'order_date' => 'date',
        'payment_details' => 'array',
    ];

    protected static function booted(): void
    {
        // No global scopes for base Order model to allow global visibility in Master/Inventory Admin
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function product()
    {
        return $this->hasOne(ProductMapping::class, 'product_name', 'item_name');
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    /**
     * Get tax breakdown for the order (CGST/SGST vs IGST)
     */
    public function getTaxBreakdown(): array
    {
        $taxAmount = (float)($this->tax_amount ?? 0);
        $sellerState = 'Tamil Nadu';
        
        // Extract state from address (simple match for now)
        $isIntraState = str_contains(strtolower($this->customer_address), strtolower($sellerState));
        
        if ($isIntraState) {
            return [
                'type' => 'intra',
                'cgst_rate' => null, // Derived from item match if needed, but splits 50/50
                'cgst_amount' => $taxAmount / 2,
                'sgst_rate' => null,
                'sgst_amount' => $taxAmount / 2,
                'igst_amount' => 0,
            ];
        }

        return [
            'type' => 'inter',
            'cgst_amount' => 0,
            'sgst_amount' => 0,
            'igst_amount' => $taxAmount,
        ];
    }
}

