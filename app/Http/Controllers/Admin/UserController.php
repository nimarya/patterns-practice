<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $this->ensureUsersAccess($request);

        $users = User::query()
            ->with('roles:id,name')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at?->toISOString(),
                'roles' => $user->roles->pluck('name')->values()->all(),
            ])
            ->values()
            ->all();

        return Inertia::render('admin/Users', [
            'users' => $users,
            'roles' => Role::query()->orderBy('name')->pluck('name')->values()->all(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->ensureUsersAccess($request);

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        if (! empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return back();
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->ensureUsersAccess($request);

        $validated = $request->validated();

        $attributes = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $attributes['password'] = $validated['password'];
        }

        $user->update($attributes);
        $user->syncRoles(! empty($validated['role']) ? [$validated['role']] : []);

        return back();
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->ensureUsersAccess($request);

        /** @var User $authenticatedUser */
        $authenticatedUser = $request->user();

        if ($authenticatedUser->is($user)) {
            return back()->withErrors([
                'delete_user' => 'You cannot delete your own account from the admin panel.',
            ]);
        }

        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->withErrors([
                'delete_user' => 'You cannot delete the last administrator.',
            ]);
        }

        $user->delete();

        return back();
    }

    private function ensureUsersAccess(Request $request): void
    {
        abort_unless($request->user()?->can('settings.users'), 403);
    }
}
