<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        'org_unit_id',
        'name',
        'primary_contact_phone',
        'address',
        'marriage_date',
    ];

    protected $casts = [
        'marriage_date' => 'date',
    ];

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'org_unit_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function memberRelationships(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'household_member')
            ->withPivot('relationship')
            ->withTimestamps();
    }
}
