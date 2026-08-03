<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HierarchyLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'hierarchy_definition_id',
        'depth',
        'name',
        'plural_name',
        'allow_institutions',
    ];

    protected $casts = [
        'depth' => 'integer',
        'allow_institutions' => 'boolean',
    ];

    public function definition(): BelongsTo
    {
        return $this->belongsTo(HierarchyDefinition::class, 'hierarchy_definition_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(OrganizationalUnit::class);
    }
}
