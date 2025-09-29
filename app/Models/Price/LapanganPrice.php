<?php

namespace App\Models\Price;

use App\Models\RentalUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LapanganPrice extends Model
{
    protected $fillable = [
        'rental_unit_id',
        'guest_price',
        'member_price',
        'member_quota',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(RentalUnit::class, 'rental_unit_id');
    }
}
