<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'alternative_number',
        'block_name',
        'address',
        'city',
        'state',
        'pincode',
        'landmark',
        'address_type',
        'address_type_display',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
