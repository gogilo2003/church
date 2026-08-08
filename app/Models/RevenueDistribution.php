<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenueDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'revenue_sharing_rule_id',
        'transaction_type',
        'transaction_id',
        'source_unit_id',
        'destination_unit_id',
        'gross_amount',
        'distributed_amount',
        'calculation_summary',
        'status',
        'settled_at',
    ];

    protected $casts = [
        'gross_amount' => 'float',
        'distributed_amount' => 'float',
        'settled_at' => 'datetime',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(RevenueSharingRule::class, 'revenue_sharing_rule_id');
    }

    public function sourceUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'source_unit_id');
    }

    public function destinationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'destination_unit_id');
    }
}
