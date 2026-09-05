<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Models\Central\Tenant;
use Illuminate\Foundation\Precognition;

/**
 * Runs FormRequest validation while the tenant database is active, so
 * rules like `unique:users,email` and `exists:roles,id` are resolved
 * against the target tenant connection instead of the central one.
 */
trait ValidatesInTenantContext
{
    public function validateResolved(): void
    {
        $tenant = Tenant::findOrFail((string) $this->route('tenant'));

        tenancy()->initialize($tenant);

        try {
            $this->prepareForValidation();

            if (! $this->passesAuthorization()) {
                $this->failedAuthorization();
            }

            $instance = $this->getValidatorInstance();

            if ($this->isPrecognitive()) {
                $instance->after(Precognition::afterValidationHook($this));
            }

            if ($instance->fails()) {
                $this->failedValidation($instance);
            }

            $this->passedValidation();
        } finally {
            tenancy()->end();
        }
    }
}
