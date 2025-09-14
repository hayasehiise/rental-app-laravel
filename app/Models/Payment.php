<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'order_id',
        'snap_token',
        'transaction_id',
        'payment_type',
        'transaction_status',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    //helper
    public function scopePaid($query)
    {
        return $query->where('transaction_status', 'capture');
    }
}
