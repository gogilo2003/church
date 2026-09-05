<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected RoleService $roleService
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status', 'sort_by', 'sort_order']);
        $users = $this->userService->getUsersPageData($filters, 15);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        $roles = $this->roleService->getAllRoles();

        return Inertia::render('Users/Create', [
            'roles' => RoleResource::collection($roles),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->createUser($request->validated());

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): Response
    {
        $user->load('roles');
        $roles = $this->roleService->getAllRoles();

        return Inertia::render('Users/Edit', [
            'user' => new UserResource($user),
            'roles' => RoleResource::collection($roles),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->updateUser($user, $request->validated());

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->userService->deleteUser($user);

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->isActive()) {
            $this->userService->suspendUser($user);
            $message = "User {$user->name} has been suspended.";
        } else {
            $this->userService->unsuspendUser($user);
            $message = "User {$user->name} has been reactivated.";
        }

        return redirect()->back()->with('success', $message);
    }

    public function resetPassword(ResetPasswordRequest $request, User $user): RedirectResponse
    {
        $this->userService->resetPassword($user, $request->validated('password'));

        return redirect()->back()->with('success', "Password for {$user->name} reset successfully.");
    }
}
