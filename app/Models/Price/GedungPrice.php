<?php

namespace App\Models\Price;

use App\Models\Booking;
use App\Models\RentalUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GedungPrice extends Model
{
    protected $fillable = [
        'rental_unit_id',
        'type',
        'pax',
        'per_day',
        'price',
    ];

    protected $casts = [
        'price' => 'integer',
        'pax' => 'integer',
        'per_day' => 'integer',
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
