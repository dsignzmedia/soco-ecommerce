<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'display_order',
        'gender_rule',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function productMappings(): HasMany
    {
        return $this->hasMany(ProductMapping::class);
    }
}

