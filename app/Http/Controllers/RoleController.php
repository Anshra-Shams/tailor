<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    /**
     * Group permissions neatly by logical domain/module
     */
    protected function getGroupedPermissions()
    {
        $permissions = Permission::orderBy('name')->get();
        $grouped = [];

        foreach ($permissions as $permission) {
            $name = $permission->name;

            if (str_contains($name, 'service')) {
                $group = 'Services';
            } elseif (str_contains($name, 'customer')) {
                $group = 'Customers';
            } elseif (str_contains($name, 'measurement')) {
                $group = 'Measurements';
            } elseif (str_contains($name, 'assign-order')) {
                $group = 'Assign Orders';
            } elseif (str_contains($name, 'order')) {
                $group = 'Orders';
            } elseif (str_contains($name, 'payment')) {
                $group = 'Payments';
            } elseif (str_contains($name, 'report')) {
                $group = 'Reports';
            } elseif (str_contains($name, 'account')) {
                $group = 'Chart of Accounts';
            } elseif (str_contains($name, 'department') || str_contains($name, 'designation') || str_contains($name, 'employee') || str_contains($name, 'attendance') || str_contains($name, 'salar')) {
                $group = 'HR Management';
            } elseif (str_contains($name, 'user') || str_contains($name, 'role') || str_contains($name, 'permission')) {
                $group = 'User Management';
            } else {
                $group = 'General';
            }

            $grouped[$group][] = $permission;
        }

        return $grouped;
    }

    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $roles = Role::withCount(['permissions', 'users'])
            ->when($search !== '', function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        return view('roles.index', compact('roles', 'search', 'totalRoles', 'totalPermissions'));
    }

    public function create()
    {
        $groupedPermissions = $this->getGroupedPermissions();
        return view('roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => trim($validated['name']),
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' created successfully!");
    }

    public function edit(Role $role)
    {
        $groupedPermissions = $this->getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Don't allow renaming Super Admin to prevent system lockout
        if ($role->name === 'Super Admin' && trim($validated['name']) !== 'Super Admin') {
            return redirect()->back()->with('error', 'The "Super Admin" role name cannot be changed.');
        }

        $role->name = trim($validated['name']);
        $role->save();

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->syncPermissions([]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' updated successfully!");
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Super Admin') {
            return redirect()->route('roles.index')->with('error', 'Super Admin role cannot be deleted!');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', "Cannot delete role '{$role->name}' because it is assigned to {$role->users()->count()} user(s).");
        }

        $roleName = $role->name;
        $role->syncPermissions([]);
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', "Role '{$roleName}' deleted successfully!");
    }
}
