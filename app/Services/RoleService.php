<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository
    ) {}

    public function getPaginatedRoles(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->roleRepository->getPaginated($filters, $perPage);
    }

    public function getAllRoles(): Collection
    {
        return $this->roleRepository->getAll();
    }

    public function findRole(int $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    public function createRole(array $data): Role
    {
        if (empty($data['name']) && ! empty($data['title'])) {
            $data['name'] = strtolower(str_replace(' ', '_', trim($data['title'])));
        }

        return $this->roleRepository->create($data);
    }

    public function updateRole(Role $role, array $data): Role
    {
        if (empty($data['name']) && ! empty($data['title'])) {
            $data['name'] = strtolower(str_replace(' ', '_', trim($data['title'])));
        }

        return $this->roleRepository->update($role, $data);
    }

    public function deleteRole(Role $role): bool
    {
        return $this->roleRepository->delete($role);
    }

    public function assignUsersToRole(Role $role, array $userIds): Role
    {
        return $this->roleRepository->syncUsers($role, $userIds);
    }
}
