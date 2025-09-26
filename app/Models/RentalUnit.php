<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'name',
        'is_available',
    ];

    // Relationship
    public function image(): HasMany
    {
        return $this->hasMany(RentalImage::class);
    }
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class, 'rental_id');
    }
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
    public function prices(): HasMany
    {
        return $this->hasMany(RentalUnitPrice::class, 'rental_unit_id');
    }

    // helper method
    public function getPriceFor(string $type): ?float
    {
        return $this->prices()
            ->where('type', $type)
            ->value('price');
    }
}
