<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\AssignRoleUsersRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService,
        protected UserService $userService
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'sort_by', 'sort_order']);
        $roles = $this->roleService->getRolesPageData($filters, 15);

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create');
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->roleService->createRole($request->validated());

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('Roles/Edit', [
            'role' => new RoleResource($role),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->roleService->updateRole($role, $request->validated());

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        try {
            $this->roleService->deleteRole($role);
            return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
        } catch (\DomainException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function assignUsers(Role $role): Response
    {
        $role->load('users');
        $allUsers = User::orderBy('name')->get();

        return Inertia::render('Roles/AssignUsers', [
            'role' => new RoleResource($role),
            'allUsers' => UserResource::collection($allUsers),
            'assignedUserIds' => $role->users->pluck('id')->toArray(),
        ]);
    }

    public function syncUsers(AssignRoleUsersRequest $request, Role $role): RedirectResponse
    {
        $this->roleService->assignUsersToRole($role, $request->validated('user_ids'));

        return redirect()->route('roles.index')->with('success', "Users assigned to role '{$role->title}' successfully.");
    }
}
