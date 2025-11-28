<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'default_cost',
        'zones',
        'overrides',
    ];

    protected $casts = [
        'zones' => 'array',
        'overrides' => 'array',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create([
            'default_cost' => 0,
            'zones' => [],
            'overrides' => [],
        ]);
    }
}

