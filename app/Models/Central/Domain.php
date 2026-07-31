<?php

declare(strict_types=1);

namespace App\Models\Central;

use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

class Domain extends BaseDomain
{
    protected $fillable = [
        'domain',
        'tenant_id',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];
}
