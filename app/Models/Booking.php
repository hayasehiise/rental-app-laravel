<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'rental_unit_id',
        'start_time',
        'end_time',
        'price',
        'discount',
        'final_price',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime:Y-m-d\TH:i:s',
        'end_time' => 'datetime:Y-m-d\TH:i:s',
    ];

    // Relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
    public function unit(): BelongsTo
    {
        return $this->belongsTo(RentalUnit::class, 'rental_unit_id');
    }

    // Helpers
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    public function calculateFinalPrice(): int
    {
        return max(0, $this->price - ($this->discount / 100 * $this->price));
    }
}
