<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'percentage',
        'start_time',
        'end_time',
        'is_member_only',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
