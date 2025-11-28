<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'properties',
        'ip_address',
        'acted_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'acted_at' => 'datetime',
    ];
}

