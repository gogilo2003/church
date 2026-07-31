<?php

declare(strict_types=1);

namespace App\Models\Central;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'data' => 'array',
    ];

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
        ];
    }
}
