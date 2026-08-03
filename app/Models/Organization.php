<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function hierarchyDefinitions(): HasMany
    {
        return $this->hasMany(HierarchyDefinition::class);
    }

    public function activeHierarchyDefinition(): HasOne
    {
        return $this->hasOne(HierarchyDefinition::class)->where('is_active', true);
    }

    public function units(): HasMany
    {
        return $this->hasMany(OrganizationalUnit::class);
    }
}
