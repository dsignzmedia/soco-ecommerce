<?php

namespace App\Models\BackToSchool;

use Illuminate\Database\Eloquent\Builder;

class Order extends \App\Models\Admin\Master\Order
{
    protected $table = 'orders';

    protected static function booted(): void
    {
        static::addGlobalScope('back_to_school_orders', function (Builder $builder) {
            $builder->where('product_type', 'back_to_school');
        });
    }
}
