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
    ];

    protected $casts = [
        'order_date' => 'date',
        'payment_details' => 'array',
    ];

    /**
     * The "booted" method of the model.
     * Add global scope to hide orders from deleted schools
     * Allow orders with NULL school_id (for BTS/Merchandise global products)
     */
    protected static function booted(): void
    {
        static::addGlobalScope('activeSchool', function (Builder $builder) {
            $builder->where(function($q) {
                // Allow orders that are not linked to any school (BTS/Merchandise global products)
                $q->whereNull('school_id')
                  // OR orders linked to an active school
                  ->orWhereHas('school', function ($query) {
                      // School model's global scope will automatically filter has_deleted = 0
                  });
            });
        });

        static::addGlobalScope('school_orders_only', function (Builder $builder) {
            $builder->where(function($q) {
                $q->whereNotIn('product_type', ['back_to_school', 'merchandised'])
                  ->orWhereNull('product_type');
            });
        });
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
}

