<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contribution extends Model
{
    use HasFactory;

    protected $casts = [
        "end_at" => "date"
    ];

    /**
     * Get the contribution_type that owns the Contribution
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contribution_type(): BelongsTo
    {
        return $this->belongsTo(ContributionType::class);
    }

    /**
     * Get all of the payments for the Contribution
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the member that owns the Contribution
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
