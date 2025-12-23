<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'product_type',
        'payment_id',
        'total_amount',
        'tax_amount',
        'shipping_cost',
        'amount_paid',
        'payment_status',
        'payment_method',
        'payment_type',
        'payment_details',
        'payment_for',
    ];

    protected $casts = [
        'payment_details' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(\App\Models\Admin\Master\Order::class);
    }
}
