<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'description',
    ];

    // Relationship
    public function units(): HasMany
    {
        return $this->hasMany(RentalUnit::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(RentalCategory::class, 'category_id');
    }
}
