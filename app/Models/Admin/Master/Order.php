<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'school_id',
        'order_date',
        'student_name',
        'grade',
        'category',
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
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

