<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\TenantUserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Operates against the currently initialized tenant database connection.
 * Callers must ensure the tenant context is active (see TenantUserService).
 */
class TenantUserRepository implements TenantUserRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('roles');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_order'] ?? 'desc';
        $allowedSorts = ['name', 'email', 'status', 'created_at'];

        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, strtolower($sortDirection) === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getRoles(): Collection
    {
        return Role::orderBy('title')->get();
    }

    public function create(array $data): User
    {
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->username = $data['username'] ?? null;
        $user->phone_number = $data['phone_number'] ?? null;
        $user->status = $data['status'] ?? 'active';
        $user->password = $data['password'];
        $user->is_admin = $data['is_admin'] ?? false;
        $user->save();

        if (! empty($data['role_ids'])) {
            $user->roles()->sync($data['role_ids']);
        }

        return $user->load('roles');
    }

    public function update(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->username = $data['username'] ?? null;
        $user->phone_number = $data['phone_number'] ?? null;

        if (isset($data['status'])) {
            $user->status = $data['status'];
        }

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        if (array_key_exists('role_ids', $data)) {
            $user->roles()->sync($data['role_ids']);
        }

        return $user->load('roles');
    }

    public function updateStatus(int $userId, string $status): User
    {
        $user = User::findOrFail($userId);
        $user->status = $status;
        $user->save();

        return $user;
    }

    public function resetPassword(int $userId, string $hashedPassword): User
    {
        $user = User::findOrFail($userId);
        $user->password = $hashedPassword;
        $user->save();

        return $user;
    }
}
