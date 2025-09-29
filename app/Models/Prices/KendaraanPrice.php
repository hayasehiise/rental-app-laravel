<?php

namespace App\Models\Prices;

use App\Models\RentalUnitPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class KendaraanPrice extends Model
{
    protected $fillable = [
        'price'
    ];

    public function rentalPrice(): MorphOne
    {
        return $this->morphOne(RentalUnitPrice::class, 'priceable');
    }
}
