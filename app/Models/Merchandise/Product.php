<?php

namespace App\Models\Merchandise;

use App\Models\Admin\Master\ProductMapping;
use Illuminate\Database\Eloquent\Builder;

class Product extends ProductMapping
{
    protected $table = 'product_mappings';

    protected static function booted()
    {
        static::addGlobalScope('merchandised', function (Builder $builder) {
            $builder->where('product_type', 'merchandised');
        });
    }
}
