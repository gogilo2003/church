<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Offering extends Model
{
    use HasFactory;

    protected $fillable = [
        'org_unit_id',
        'user_id',
        'offering_type_id',
        'amount',
        'offering_date',
    ];

    /**
     * Get the user that owns the Offering
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the type that owns the Offering
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(OfferingType::class, 'offering_type_id', 'id');
    }

    public function offeringDate(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($value)->isoFormat('dddd, D MMM, Y'),
            set: fn ($value) => Carbon::parse($value),
        );
    }
}
