<?php

namespace App\Models\Price;

use App\Models\Booking;
use App\Models\RentalUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KendaraanPrice extends Model
{
    protected $fillable = [
        'rental_unit_id',
        'price',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(RentalUnit::class, 'rental_unit_id');
    }
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'price_id');
    }
}
