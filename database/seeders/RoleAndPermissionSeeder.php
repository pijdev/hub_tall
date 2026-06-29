<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            ['name' => 'view-users', 'guard_name' => 'web', 'description' => 'Visualizar usuários', 'group' => 'Usuários'],
            ['name' => 'create-users', 'guard_name' => 'web', 'description' => 'Criar usuários', 'group' => 'Usuários'],
            ['name' => 'edit-users', 'guard_name' => 'web', 'description' => 'Editar usuários', 'group' => 'Usuários'],
            ['name' => 'delete-users', 'guard_name' => 'web', 'description' => 'Excluir usuários', 'group' => 'Usuários'],

            // Role management
            ['name' => 'view-roles', 'guard_name' => 'web', 'description' => 'Visualizar papéis', 'group' => 'Papéis'],
            ['name' => 'create-roles', 'guard_name' => 'web', 'description' => 'Criar papéis', 'group' => 'Papéis'],
            ['name' => 'edit-roles', 'guard_name' => 'web', 'description' => 'Editar papéis', 'group' => 'Papéis'],
            ['name' => 'delete-roles', 'guard_name' => 'web', 'description' => 'Excluir papéis', 'group' => 'Papéis'],

            // Permission management
            ['name' => 'view-permissions', 'guard_name' => 'web', 'description' => 'Visualizar permissões', 'group' => 'Permissões'],
            ['name' => 'create-permissions', 'guard_name' => 'web', 'description' => 'Criar permissões', 'group' => 'Permissões'],
            ['name' => 'edit-permissions', 'guard_name' => 'web', 'description' => 'Editar permissões', 'group' => 'Permissões'],
            ['name' => 'delete-permissions', 'guard_name' => 'web', 'description' => 'Excluir permissões', 'group' => 'Permissões'],

            // Branding
            ['name' => 'view-branding', 'guard_name' => 'web', 'description' => 'Visualizar e editar configurações de marca', 'group' => 'Branding'],
        ];

        foreach ($permissions as $data) {
            Permission::firstOrCreate(
                ['name' => $data['name'], 'guard_name' => 'web'],
                $data,
            );
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web'],
            ['description' => 'Acesso total ao sistema', 'level' => 100],
        );

        $admin = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['description' => 'Acesso administrativo', 'level' => 50],
        );

        Role::firstOrCreate(
            ['name' => 'User', 'guard_name' => 'web'],
            ['description' => 'Usuário padrão', 'level' => 1],
        );

        // Assign all permissions to Super Admin
        $superAdmin->permissions()->sync(Permission::all());

        // Assign user management permissions to Admin
        $admin->permissions()->sync(
            Permission::whereIn('name', [
                'view-users',
                'view-roles',
                'view-permissions',
                'view-branding',
            ])->get(),
        );

        // Assign Super Admin role to the first user
        if ($user = User::first()) {
            $user->roles()->syncWithoutDetaching([$superAdmin->id]);
        }
    }
}
