<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContributionType extends Model
{
    use HasFactory;

    protected $casts = ['deadline' => 'date'];

    /**
     * Get all of the contributions for the ContributionType
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }
}
