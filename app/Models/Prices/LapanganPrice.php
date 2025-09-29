<?php

namespace App\Models\Prices;

use App\Models\RentalUnitPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class LapanganPrice extends Model
{
    protected $fillable = [
        'guest_price',
        'member_price',
        'membership_quota',
    ];

    public function rentalPrice(): MorphOne
    {
        return $this->morphOne(RentalUnitPrice::class, 'priceable');
    }
}
