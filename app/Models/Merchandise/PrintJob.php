<?php

namespace App\Models\Merchandise;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Master\Order;
use App\Models\Admin\Master\ProductMapping;

class PrintJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_mapping_id',
        'status',
        'print_file_path',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductMapping::class, 'product_mapping_id');
    }
}
