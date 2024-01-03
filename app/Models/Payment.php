<?php

namespace App\Models;

use App\Events\PaymentRegistered;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $dispatchesEvents = [
        'created' => PaymentRegistered::class
    ];

    /**
     * Get the contribution that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contribution(): BelongsTo
    {
        return $this->belongsTo(Contribution::class);
    }
}
