<?php

namespace App\Models\Prices;

use App\Models\RentalUnitPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class GedungPrice extends Model
{
    protected $fillable = [
        'type',
        'pax',
        'day_number',
        'price',
    ];

    public function rentalPrice(): MorphOne
    {
        return $this->morphOne(RentalUnitPrice::class, 'priceable');
    }
}
