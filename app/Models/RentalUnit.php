<?php

namespace App\Models;

use App\Models\Price\GedungPrice;
use App\Models\Price\KendaraanPrice;
use App\Models\Price\LapanganPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    public function lapanganPrice(): HasOne
    {
        return $this->hasOne(LapanganPrice::class);
    }
    public function gedungPrice(): HasOne
    {
        return $this->hasOne(GedungPrice::class);
    }
    public function kendaraanPrice(): HasOne
    {
        return $this->hasOne(KendaraanPrice::class);
    }
}
