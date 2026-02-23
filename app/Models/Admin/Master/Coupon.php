<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'discount_amount',
        'discount_percentage',
        'valid_from',
        'valid_to',
        'is_active',
        'usage_limit',
        'used_count',
        'description',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_from && now()->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_to && now()->gt($this->valid_to)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
