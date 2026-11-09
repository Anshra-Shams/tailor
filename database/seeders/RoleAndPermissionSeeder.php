<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions by module
        $modules = [
            'Services' => [
                'view-services',
                'create-services',
                'edit-services',
                'delete-services',
            ],
            'Customers' => [
                'view-customers',
                'create-customers',
                'edit-customers',
                'delete-customers',
            ],
            'Measurements' => [
                'view-measurements',
                'create-measurements',
                'edit-measurements',
                'delete-measurements',
            ],
            'Orders' => [
                'view-orders',
                'create-orders',
                'edit-orders',
                'delete-orders',
            ],
            'Assign Orders' => [
                'view-assign-orders',
                'manage-assign-orders',
            ],
            'Payments' => [
                'view-payments',
                'create-payments',
            ],
            'Reports' => [
                'view-reports',
            ],
            'Accounts' => [
                'view-accounts',
                'manage-accounts',
            ],
            'HR' => [
                'manage-departments',
                'manage-designations',
                'manage-employees',
                'manage-attendance',
                'manage-salaries',
            ],
            'User Management' => [
                'view-users',
                'create-users',
                'edit-users',
                'delete-users',
                'view-roles',
                'create-roles',
                'edit-roles',
                'delete-roles',
                'view-permissions',
                'create-permissions',
                'edit-permissions',
                'delete-permissions',
            ],
        ];

        $allPermissions = [];
        foreach ($modules as $module => $permissions) {
            foreach ($permissions as $permissionName) {
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
                $allPermissions[] = $permission;
            }
        }

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminPermissions = Permission::whereNotIn('name', [
            'delete-roles',
            'delete-permissions',
        ])->get();
        $adminRole->syncPermissions($adminPermissions);

        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $managerPermissions = Permission::whereIn('name', [
            'view-services', 'create-services', 'edit-services',
            'view-customers', 'create-customers', 'edit-customers',
            'view-measurements', 'create-measurements', 'edit-measurements',
            'view-orders', 'create-orders', 'edit-orders',
            'view-assign-orders', 'manage-assign-orders',
            'view-payments', 'create-payments',
            'view-accounts',
            'manage-attendance',
        ])->get();
        $managerRole->syncPermissions($managerPermissions);

        $tailorRole = Role::firstOrCreate(['name' => 'Tailor / Worker', 'guard_name' => 'web']);
        $tailorPermissions = Permission::whereIn('name', [
            'view-measurements',
            'view-orders',
            'view-assign-orders',
        ])->get();
        $tailorRole->syncPermissions($tailorPermissions);

        // Assign Super Admin role to the default user
        $adminUser = User::where('email', 'admin@tailor.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($superAdminRole);
        } else {
            // Also check first user if email doesn't match
            $firstUser = User::first();
            if ($firstUser) {
                $firstUser->assignRole($superAdminRole);
            }
        }
    }
}
