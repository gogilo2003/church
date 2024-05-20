<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offering extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the Offering
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the type that owns the Offering
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(OfferingType::class, 'offering_type_id', 'id');
    }
    public function offeringDate(): Attribute
    {
        return new Attribute(
            get: fn($value) => Carbon::parse($value),
            set: fn($value) => Carbon::parse($value),
        );
    }
}
