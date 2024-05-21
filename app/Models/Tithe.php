<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tithe extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the Tithe
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tithedOn(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->isoFormat('dddd, D MMM, Y'),
            set: fn($value) => Carbon::parse($value),
        );
    }
}
