<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

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
        'has_deleted',
        'contact_name',
        'contact_email',
        'contact_phone',
        'notes',
        'shipping_zone_id',
    ];

    /**
     * The "booted" method of the model.
     * Add global scope to hide deleted schools everywhere
     */
    protected static function booted(): void
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('has_deleted', 0);
        });
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class)->orderBy('display_order');
    }

    public function productMappings(): HasMany
    {
        return $this->hasMany(ProductMapping::class);
    }

    public function shippingZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }
}

