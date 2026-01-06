<?php

namespace App\Models\BackToSchool;

use App\Models\Admin\Master\ProductMapping;
use Illuminate\Database\Eloquent\Builder;

class Product extends ProductMapping
{
    protected $table = 'product_mappings';

    protected static function booted(): void
    {
        static::addGlobalScope('back_to_school', function (Builder $builder) {
            $builder->where('product_type', 'back_to_school');
        });
    }
}
