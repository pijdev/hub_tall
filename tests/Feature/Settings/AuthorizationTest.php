<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

/**
 * Helper to create the Super Admin role and assign it to a user.
 */
function createSuperAdminUser(): User
{
    $user = User::factory()->create();
    $superAdmin = Role::firstOrCreate(
        ['name' => 'Super Admin', 'guard_name' => 'web'],
        ['level' => 100, 'description' => 'Super Admin'],
    );
    $user->roles()->sync([$superAdmin->id]);

    return $user;
}

/**
 * Helper to create a role with a specific permission.
 */
function createRoleWithPermission(string $permissionName, string $group = 'Testes'): Role
{
    $role = Role::firstOrCreate(
        ['name' => 'TestRole-'.uniqid(), 'guard_name' => 'web'],
        ['level' => 1],
    );
    $perm = Permission::firstOrCreate(
        ['name' => $permissionName, 'guard_name' => 'web'],
        ['group' => $group],
    );
    $role->permissions()->sync([$perm->id]);

    return $role;
}

// ---------------------------------------------------------------------------
// Role CRUD
// ---------------------------------------------------------------------------

test('super_admin_can_view_roles_index', function () {
    $user = createSuperAdminUser();

    $this->actingAs($user)
        ->get(route('settings.roles.index'))
        ->assertOk();
});

test('user_with_permission_can_view_roles_index', function () {
    $role = createRoleWithPermission('view-roles', 'Papéis');
    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $this->actingAs($user)
        ->get(route('settings.roles.index'))
        ->assertOk();
});

test('user_without_permission_cannot_view_roles_index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('settings.roles.index'))
        ->assertForbidden();
});

test('super_admin_can_create_role', function () {
    $user = createSuperAdminUser();

    $this->actingAs($user);

    Livewire::test('pages::settings.roles.create')
        ->set('name', 'Moderator')
        ->set('level', 10)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('settings.roles.index'));

    expect(Role::where('name', 'Moderator')->exists())->toBeTrue();
});

test('user_without_permission_cannot_create_role', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.roles.create')
        ->set('name', 'Moderator')
        ->set('level', 10)
        ->call('save')
        ->assertForbidden();
});

test('super_admin_can_edit_role', function () {
    $user = createSuperAdminUser();

    $role = Role::firstOrCreate(
        ['name' => 'Editor', 'guard_name' => 'web'],
        ['level' => 5, 'description' => 'Editor role'],
    );

    $this->actingAs($user);

    Livewire::test('pages::settings.roles.edit', ['role' => $role])
        ->set('name', 'Updated Role')
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('settings.roles.index'));

    expect($role->fresh()->name)->toEqual('Updated Role');
});

test('user_without_permission_cannot_edit_role', function () {
    $user = User::factory()->create();
    $role = Role::firstOrCreate(
        ['name' => 'Editor2', 'guard_name' => 'web'],
        ['level' => 5, 'description' => 'Editor role'],
    );

    $this->actingAs($user)
        ->get(route('settings.roles.edit', ['role' => $role]))
        ->assertForbidden();
});

test('super_admin_can_delete_role', function () {
    $user = createSuperAdminUser();

    $role = Role::firstOrCreate(
        ['name' => 'ToDelete', 'guard_name' => 'web'],
        ['level' => 5, 'description' => 'Will be deleted'],
    );

    $this->actingAs($user);

    Livewire::test('pages::settings.roles.index')
        ->call('delete', $role->id);

    expect(Role::find($role->id))->toBeNull();
});

test('super_admin_cannot_delete_itself', function () {
    $user = createSuperAdminUser();

    $superAdmin = Role::where('name', 'Super Admin')->first();

    $this->actingAs($user);

    Livewire::test('pages::settings.roles.index')
        ->call('delete', $superAdmin->id);

    expect(Role::find($superAdmin->id))->not->toBeNull();
});

// ---------------------------------------------------------------------------
// Permission CRUD
// ---------------------------------------------------------------------------

test('super_admin_can_view_permissions_index', function () {
    $user = createSuperAdminUser();

    $this->actingAs($user)
        ->get(route('settings.permissions.index'))
        ->assertOk();
});

test('user_with_permission_can_view_permissions_index', function () {
    $role = createRoleWithPermission('view-permissions', 'Permissões');
    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $this->actingAs($user)
        ->get(route('settings.permissions.index'))
        ->assertOk();
});

test('user_without_permission_cannot_view_permissions_index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('settings.permissions.index'))
        ->assertForbidden();
});

test('super_admin_can_create_permission', function () {
    $user = createSuperAdminUser();

    $this->actingAs($user);

    Livewire::test('pages::settings.permissions.create')
        ->set('name', 'manage-reports')
        ->set('group', 'Relatórios')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('settings.permissions.index'));

    expect(Permission::where('name', 'manage-reports')->exists())->toBeTrue();
});

test('user_without_permission_cannot_create_permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.permissions.create')
        ->set('name', 'manage-reports')
        ->set('group', 'Relatórios')
        ->call('save')
        ->assertForbidden();
});

test('super_admin_can_edit_permission', function () {
    $user = createSuperAdminUser();

    $permission = Permission::firstOrCreate(
        ['name' => 'manage-test', 'guard_name' => 'web'],
        ['group' => 'Testes', 'description' => 'Test permission'],
    );

    $this->actingAs($user);

    Livewire::test('pages::settings.permissions.edit', ['permission' => $permission])
        ->set('description', 'Updated description')
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('settings.permissions.index'));

    expect($permission->fresh()->description)->toEqual('Updated description');
});

test('user_without_permission_cannot_edit_permission', function () {
    $user = User::factory()->create();
    $permission = Permission::firstOrCreate(
        ['name' => 'manage-test-edit', 'guard_name' => 'web'],
        ['group' => 'Testes', 'description' => 'Test permission'],
    );

    $this->actingAs($user)
        ->get(route('settings.permissions.edit', ['permission' => $permission]))
        ->assertForbidden();
});

test('super_admin_can_delete_permission', function () {
    $user = createSuperAdminUser();

    $permission = Permission::firstOrCreate(
        ['name' => 'manage-test-delete', 'guard_name' => 'web'],
        ['group' => 'Testes', 'description' => 'Test permission'],
    );

    $this->actingAs($user);

    Livewire::test('pages::settings.permissions.index')
        ->call('delete', $permission->id);

    expect(Permission::find($permission->id))->toBeNull();
});

// ---------------------------------------------------------------------------
// Authorization helpers
// ---------------------------------------------------------------------------

test('is_super_admin_returns_true_for_super_admin_role', function () {
    $user = createSuperAdminUser();

    expect($user->isSuperAdmin())->toBeTrue();
});

test('is_super_admin_returns_false_for_regular_user', function () {
    $user = User::factory()->create();

    expect($user->isSuperAdmin())->toBeFalse();
});

test('has_permission_checks_user_roles_permissions', function () {
    $role = createRoleWithPermission('view-reports', 'Relatórios');
    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    expect($user->hasPermission('view-reports'))->toBeTrue();
    expect($user->hasPermission('delete-reports'))->toBeFalse();
});

test('super_admin_has_all_permissions_via_bypass', function () {
    $user = createSuperAdminUser();

    // hasPermission checks the database directly, not via Gate
    // The Gate::before bypass only works for authorization checks (Gate, policies, @can)
    expect($user->hasPermission('non-existent-permission'))->toBeFalse();

    // But Gate::allows should return true for Super Admin
    $this->actingAs($user);
    expect(Gate::allows('non-existent-permission'))->toBeTrue();
    expect(Gate::allows('view-roles'))->toBeTrue();
});

test('permission_middleware_blocks_unauthorized_access', function () {
    $role = createRoleWithPermission('view-roles', 'Papéis');
    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $this->actingAs($user);

    // User has view-roles but not create-roles
    $this->get(route('settings.roles.create'))->assertForbidden();

    // User has view-roles so can view the index
    $this->get(route('settings.roles.index'))->assertOk();
});
