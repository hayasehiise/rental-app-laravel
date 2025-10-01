<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'rental_unit_id',
        'discount_id',
        'start_time',
        'end_time',
        'price',
        'final_price',
        'status',
        'parent_booking_id'
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
    public function reminders(): HasMany
    {
        return $this->hasMany(BookingReminder::class);
    }
    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'booking_discounts', 'booking_id', 'discount_id')->withTimestamps();
    }
    public function parentBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'parent_booking_id', 'id');
    }

    // Helpers
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
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
