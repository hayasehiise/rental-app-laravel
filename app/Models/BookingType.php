<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingType extends Model
{
    /** @use HasFactory<\Database\Factories\BookingTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'monthly_limit'
    ];

    public function booking(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
