<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function getUsersPageData(array $filters = [], int $perPage = 15): array
    {
        $users = UserResource::collection($this->userRepository->getPaginated($filters, $perPage))
            ->response()
            ->getData(true);

        return array_merge($users['meta'], [
            'data' => $users['data'],
            'links' => $users['meta']['links'],
        ]);
    }

    public function findUser(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->create($data);
    }

    public function updateUser(User $user, array $data): User
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepository->update($user, $data);
    }

    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }

    public function suspendUser(User $user): User
    {
        return $this->userRepository->updateStatus($user, 'suspended');
    }

    public function unsuspendUser(User $user): User
    {
        return $this->userRepository->updateStatus($user, 'active');
    }

    public function resetPassword(User $user, string $newPassword): User
    {
        $hashedPassword = Hash::make($newPassword);

        return $this->userRepository->resetPassword($user, $hashedPassword);
    }
}
