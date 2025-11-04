<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view-dashboard',
            'manage-transactions',
            'view-transactions',
            'create-transactions',
            'edit-transactions',
            'delete-transactions',
            'manage-users',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'manage-categories',
            'view-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // Admin Role - Full Access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // User Role - Limited Access
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view-dashboard',
            'view-transactions',
            'create-transactions',
            'edit-transactions',
            'view-reports',
        ]);

        // Manager Role - Moderate Access
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view-dashboard',
            'manage-transactions',
            'view-transactions',
            'create-transactions',
            'edit-transactions',
            'delete-transactions',
            'view-users',
            'view-reports',
            'manage-categories',
        ]);

        // Create Default Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@cashflow.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Create Default Manager User
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@cashflow.com',
            'password' => Hash::make('password'),
        ]);
        $manager->assignRole('manager');

        // Create Default User
        $user = User::create([
            'name' => 'User',
            'email' => 'user@cashflow.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('user');
    }
}
