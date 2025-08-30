<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    // Relationship
    public function units(): HasMany
    {
        return $this->hasMany(RentalUnit::class);
    }
    public function images(): HasMany
    {
        return $this->hasMany(RentalImage::class);
    }
}
