<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
}
