<?php

namespace App\Models\Price;

use App\Models\RentalUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
