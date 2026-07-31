<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferingType extends Model
{
    use HasFactory;

    /**
     * Get all of the offerings for the OfferingType
     */
    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class, 'offering_type_id', 'id');
    }
}
