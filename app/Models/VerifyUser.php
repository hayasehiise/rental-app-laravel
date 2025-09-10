<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerifyUser extends Model
{
    protected $table = 'verified_user';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'verified_status',
        'verification_token',
        'verification_expire_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
