<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalUnit extends Model
{
    protected $fillable = [
        'rental_id',
        'name',
        'price',
        'is_available',
    ];

    // Relationship
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }
}
