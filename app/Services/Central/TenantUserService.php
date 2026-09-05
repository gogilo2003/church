<?php

declare(strict_types=1);

namespace App\Services\Central;

use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\Central\Tenant;
use App\Repositories\Contracts\TenantUserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

final class TenantUserService
{
    public function __construct(
        private readonly TenantUserRepositoryInterface $userRepository
    ) {}

    public function getUsersPageData(Tenant $tenant, array $filters): array
    {
        return $tenant->run(function () use ($filters): array {
            $users = $this->userRepository->getPaginated($filters, 15);
            $usersData = UserResource::collection($users)->response()->getData(true);
            $rolesData = RoleResource::collection($this->userRepository->getRoles())->toArray(request());

            return [
                'users' => array_merge($usersData['meta'], [
                    'data' => $usersData['data'],
                    'links' => $usersData['meta']['links'],
                ]),
                'roles' => ['data' => $rolesData],
            ];
        });
    }

    public function createUser(Tenant $tenant, array $data): void
    {
        $data['password'] = Hash::make($data['password']);

        $tenant->run(fn () => $this->userRepository->create($data));
    }

    public function updateUser(Tenant $tenant, int $userId, array $data): void
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $tenant->run(fn () => $this->userRepository->update($userId, $data));
    }

    public function changeStatus(Tenant $tenant, int $userId, string $status): void
    {
        $tenant->run(fn () => $this->userRepository->updateStatus($userId, $status));
    }

    public function resetPassword(Tenant $tenant, int $userId, string $password): void
    {
        $hashedPassword = Hash::make($password);

        $tenant->run(fn () => $this->userRepository->resetPassword($userId, $hashedPassword));
    }
}
