<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contribution extends Model
{
    use HasFactory;

    /**
     * Get the contribution_type that owns the Contribution
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contribution_type(): BelongsTo
    {
        return $this->belongsTo(ContributionType::class);
    }
}
