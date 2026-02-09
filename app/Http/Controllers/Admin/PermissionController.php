<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Http\Requests\Admin\UpdateRolePermissionsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(Request $request): Response
    {
        $this->ensurePermissionsAccess($request);

        $roles = Role::query()
            ->with(['permissions:id,name'])
            ->withCount('users')
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => $role->users_count,
                'permissions' => $role->permissions->pluck('name')->values()->all(),
            ])
            ->values()
            ->all();

        $permissions = Permission::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(fn (Permission $permission): array => [
                'id' => $permission->id,
                'name' => $permission->name,
            ])
            ->values()
            ->all();

        return Inertia::render('admin/Permissions', [
            'permissions' => $permissions,
            'roles' => $roles,
        ]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->ensurePermissionsAccess($request);

        Permission::query()->create([
            'name' => trim($request->validated('name')),
        ]);

        return back();
    }

    public function updateRolePermissions(UpdateRolePermissionsRequest $request, Role $role): RedirectResponse
    {
        $this->ensurePermissionsAccess($request);

        $role->syncPermissions($request->validated('permissions'));

        return back();
    }

    public function updatePermission(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->ensurePermissionsAccess($request);

        $permission->update([
            'name' => trim($request->validated('name')),
        ]);

        return back();
    }

    public function storeRole(StoreRoleRequest $request): RedirectResponse
    {
        $this->ensurePermissionsAccess($request);

        $validated = $request->validated();

        $role = Role::query()->create([
            'name' => trim($validated['name']),
        ]);

        $permissions = collect($validated['permissions'] ?? [])
            ->filter(fn (mixed $permission): bool => is_string($permission) && $permission !== '')
            ->values()
            ->all();

        $role->syncPermissions($permissions);

        return back();
    }

    public function destroyPermission(Request $request, Permission $permission): RedirectResponse
    {
        $this->ensurePermissionsAccess($request);

        $permission->delete();

        return back();
    }

    public function destroyRole(Request $request, Role $role): RedirectResponse
    {
        $this->ensurePermissionsAccess($request);

        $role->delete();

        return back();
    }

    private function ensurePermissionsAccess(Request $request): void
    {
        abort_unless($request->user()?->can('settings.permissions'), 403);
    }
}
