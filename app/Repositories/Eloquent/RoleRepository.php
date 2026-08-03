<?php

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Role::withCount('users');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_order'] ?? 'desc';
        $allowedSorts = ['name', 'title', 'created_at', 'users_count'];

        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, strtolower($sortDirection) === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getAll(): Collection
    {
        return Role::orderBy('title')->get();
    }

    public function findById(int $id): ?Role
    {
        return Role::with('users')->find($id);
    }

    public function create(array $data): Role
    {
        $role = new Role();
        $role->name = $data['name'];
        $role->title = $data['title'] ?? $data['name'];
        $role->display_name = $data['display_name'] ?? $data['title'] ?? $data['name'];
        $role->description = $data['description'] ?? null;
        $role->is_system_role = $data['is_system_role'] ?? false;
        $role->permissions = $data['permissions'] ?? [];
        $role->save();

        return $role;
    }

    public function update(Role $role, array $data): Role
    {
        $role->name = $data['name'];
        $role->title = $data['title'] ?? $data['name'];
        $role->display_name = $data['display_name'] ?? $data['title'] ?? $data['name'];
        $role->description = $data['description'] ?? null;
        
        if (array_key_exists('is_system_role', $data)) {
            $role->is_system_role = $data['is_system_role'];
        }

        if (array_key_exists('permissions', $data)) {
            $role->permissions = $data['permissions'];
        }

        $role->save();

        return $role;
    }

    public function delete(Role $role): bool
    {
        if ($role->is_system_role) {
            throw new \DomainException('System roles cannot be deleted.');
        }

        return (bool) $role->delete();
    }

    public function syncUsers(Role $role, array $userIds): Role
    {
        $role->users()->sync($userIds);

        return $role->load('users');
    }
}
