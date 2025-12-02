<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'board',
        'city',
        'state',
        'status',
        'contact_name',
        'contact_email',
        'contact_phone',
        'notes',
    ];

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class)->orderBy('display_order');
    }

    public function productMappings(): HasMany
    {
        return $this->hasMany(ProductMapping::class);
    }
}

