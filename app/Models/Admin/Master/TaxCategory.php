<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(TaxAssignment::class);
    }
}

