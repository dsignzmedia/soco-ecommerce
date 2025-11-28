<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cost',
        'free_shipping',
        'free_threshold',
    ];

    protected $casts = [
        'free_shipping' => 'boolean',
    ];

    public function pincodes(): HasMany
    {
        return $this->hasMany(ShippingZonePincode::class);
    }
}

