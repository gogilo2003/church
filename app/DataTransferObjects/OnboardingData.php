<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class OnboardingData
{
    public function __construct(
        public string $churchName,
        public string $subdomain,
        public string $adminName,
        public string $adminEmail,
        public string $adminPassword,
        public ?string $phone = null,
        public bool $isCentralAdminCreated = false,
    ) {}

    public function toArray(): array
    {
        return [
            'church_name' => $this->churchName,
            'subdomain' => $this->subdomain,
            'admin_name' => $this->adminName,
            'admin_email' => $this->adminEmail,
            'phone' => $this->phone,
            'is_central_admin_created' => $this->isCentralAdminCreated,
        ];
    }
}
