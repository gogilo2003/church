<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenueSharingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'source_type',
        'source_id',
        'source_scope_type',
        'source_scope_id',
        'destination_type',
        'destination_id',
        'direction',
        'priority',
        'calculation_type',
        'value',
        'min_collection_threshold',
        'max_cap_amount',
        'is_active',
    ];

    protected $casts = [
        'value' => 'float',
        'min_collection_threshold' => 'float',
        'max_cap_amount' => 'float',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function sourceScopeLevel(): BelongsTo
    {
        return $this->belongsTo(HierarchyLevel::class, 'source_scope_id');
    }

    public function sourceScopeUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'source_scope_id');
    }

    public function destinationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'destination_id');
    }

    public function destinationLevel(): BelongsTo
    {
        return $this->belongsTo(HierarchyLevel::class, 'destination_id');
    }

    public function offeringType(): BelongsTo
    {
        return $this->belongsTo(OfferingType::class, 'source_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'source_id');
    }
}
