<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<string, string>
     */
    protected array $attributes = [
        'name' => 'Updated User',
        'email' => 'updated@example.com',
        'password' => 'updated-password',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('settings.users');
        Permission::findOrCreate('settings.permissions');
        Role::findOrCreate('admin')->syncPermissions(['settings.users', 'settings.permissions']);
    }

    public function test_admin_can_view_users_page(): void
    {
        $admin = $this->createAdminUser();
        User::factory()->count(2)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('admin/Users')
            ->has('users', 3)
            ->has('roles')
        );
    }

    public function test_non_admin_cannot_access_admin_users_page(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_user_without_settings_users_permission_cannot_access_admin_users_page(): void
    {
        $limitedRole = Role::findOrCreate('limited-user-settings');
        $limitedRole->givePermissionTo('settings.permissions');

        $user = User::factory()->create();
        $user->assignRole($limitedRole);

        $response = $this->actingAs($user)
            ->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_user(): void
    {
        $admin = $this->createAdminUser();
        Role::findOrCreate('manager');

        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'new-user@example.com',
                'password' => 'new-password',
                'role' => 'manager',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'new-user@example.com',
        ]);
        $this->assertTrue(User::where('email', 'new-user@example.com')->firstOrFail()->hasRole('manager'));
    }

    public function test_admin_can_update_user(): void
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        Role::findOrCreate('manager');

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                ...$this->attributes,
                'role' => 'manager',
            ]);

        $response->assertRedirect();

        $updatedUser = $user->fresh();
        $this->assertNotNull($updatedUser);
        $this->assertSame($this->attributes['name'], $updatedUser->name);
        $this->assertSame($this->attributes['email'], $updatedUser->email);
        $this->assertTrue(Hash::check($this->attributes['password'], $updatedUser->password));
        $this->assertTrue($updatedUser->hasRole('manager'));
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect();
        $this->assertModelMissing($user);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $admin));

        $response->assertSessionHasErrors('delete_user');
        $this->assertNotNull($admin->fresh());
    }

    public function test_admin_can_view_permissions_page(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->get(route('admin.permissions.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('admin/Permissions')
            ->has('permissions')
            ->has('roles')
        );
    }

    public function test_user_without_settings_permissions_cannot_access_permissions_page(): void
    {
        $limitedRole = Role::findOrCreate('limited-permissions-settings');
        $limitedRole->givePermissionTo('settings.users');

        $user = User::factory()->create();
        $user->assignRole($limitedRole);

        $response = $this->actingAs($user)
            ->get(route('admin.permissions.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_permission(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->post(route('admin.permissions.store'), [
                'name' => 'users.manage',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'name' => 'users.manage',
        ]);
    }

    public function test_admin_can_update_role_permissions(): void
    {
        $admin = $this->createAdminUser();
        $role = Role::findOrCreate('manager');

        $viewPermission = Permission::create(['name' => 'users.view']);
        $updatePermission = Permission::create(['name' => 'users.update']);
        $role->givePermissionTo($viewPermission);

        $response = $this->actingAs($admin)
            ->put(route('admin.roles.permissions.update', $role), [
                'permissions' => [$updatePermission->name],
            ]);

        $response->assertRedirect();

        $this->assertSame(
            [$updatePermission->name],
            $role->fresh()->permissions->pluck('name')->values()->all()
        );
    }

    public function test_admin_can_update_permission_name(): void
    {
        $admin = $this->createAdminUser();
        $permission = Permission::create(['name' => 'courses.view']);

        $response = $this->actingAs($admin)
            ->put(route('admin.permissions.update', $permission), [
                'name' => 'courses.read',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'courses.read',
        ]);
    }

    public function test_admin_can_create_role(): void
    {
        $admin = $this->createAdminUser();
        $permission = Permission::create(['name' => 'courses.view']);

        $response = $this->actingAs($admin)
            ->post(route('admin.roles.store'), [
                'name' => 'mentor',
                'permissions' => [$permission->name],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'name' => 'mentor',
        ]);
        $this->assertTrue(Role::where('name', 'mentor')->firstOrFail()->hasPermissionTo($permission->name));
    }

    public function test_admin_can_delete_permission(): void
    {
        $admin = $this->createAdminUser();
        $permission = Permission::create(['name' => 'lessons.view']);

        $response = $this->actingAs($admin)
            ->delete(route('admin.permissions.destroy', $permission));

        $response->assertRedirect();
        $this->assertModelMissing($permission);
    }

    public function test_admin_can_delete_role(): void
    {
        $admin = $this->createAdminUser();
        $role = Role::findOrCreate('mentor');

        $response = $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect();
        $this->assertModelMissing($role);
    }

    private function createAdminUser(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }
}
