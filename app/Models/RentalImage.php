<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalImage extends Model
{
    protected $fillable = [
        'rental_unit_id',
        'path',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(RentalUnit::class);
    }
}
