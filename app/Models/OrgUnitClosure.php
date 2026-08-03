<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrgUnitClosure extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'ancestor_id',
        'descendant_id',
        'depth',
    ];

    public function ancestor(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'ancestor_id');
    }

    public function descendant(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'descendant_id');
    }
}
