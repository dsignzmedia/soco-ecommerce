<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DtdcApiLog extends Model
{
    protected $fillable = [
        'request_data',
        'response_data',
        'status',
        'reference_number',
        'awb'
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];
}
