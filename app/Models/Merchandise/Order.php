<?php

namespace App\Models\Merchandise;

use Illuminate\Database\Eloquent\Builder;

class Order extends \App\Models\Admin\Master\Order
{
    protected $table = 'orders';

    protected static function booted(): void
    {
        static::addGlobalScope('merchandise_orders', function (Builder $builder) {
            $builder->where('product_type', 'merchandised');
        });
    }

    public function printJob()
    {
        return $this->hasOne(PrintJob::class, 'order_id');
    }
}
