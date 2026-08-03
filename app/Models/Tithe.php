<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Tithe extends Model
{
    use HasFactory;

    protected $fillable = [
        'org_unit_id',
        'user_id',
        'member_id',
        'amount',
        'tithed_on',
    ];

    /**
     * Get the user that owns the Tithe
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tithedOn(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->isoFormat('dddd, D MMM, Y'),
            set: fn ($value) => Carbon::parse($value),
        );
    }
}
