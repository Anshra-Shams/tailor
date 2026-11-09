<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $groupFilter = trim($request->query('group', ''));

        $permissionsQuery = Permission::with('roles')
            ->when($search !== '', function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            });

        $allPermissions = $permissionsQuery->orderBy('name')->get();

        // Helper to infer module group
        $getGroup = function ($name) {
            if (str_contains($name, 'service')) return 'Services';
            if (str_contains($name, 'customer')) return 'Customers';
            if (str_contains($name, 'measurement')) return 'Measurements';
            if (str_contains($name, 'assign-order')) return 'Assign Orders';
            if (str_contains($name, 'order')) return 'Orders';
            if (str_contains($name, 'report')) return 'Reports';
            if (str_contains($name, 'payment')) return 'Payments';
            if (str_contains($name, 'account')) return 'Chart of Accounts';
            if (str_contains($name, 'department') || str_contains($name, 'designation') || str_contains($name, 'employee') || str_contains($name, 'attendance') || str_contains($name, 'salar')) return 'HR Management';
            if (str_contains($name, 'user') || str_contains($name, 'role') || str_contains($name, 'permission')) return 'User Management';
            return 'General';
        };

        if ($groupFilter !== '') {
            $allPermissions = $allPermissions->filter(function ($item) use ($getGroup, $groupFilter) {
                return $getGroup($item->name) === $groupFilter;
            });
        }

        // Manual pagination or grouped collection
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $currentPageItems = $allPermissions->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $permissions = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $allPermissions->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $totalPermissions = Permission::count();
        $totalRoles = Role::count();

        $groups = [
            'Services',
            'Customers',
            'Measurements',
            'Orders',
            'Assign Orders',
            'Payments',
            'Chart of Accounts',
            'HR Management',
            'User Management',
            'General',
        ];

        return view('permissions.index', compact('permissions', 'search', 'groupFilter', 'groups', 'totalPermissions', 'totalRoles', 'getGroup'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $name = strtolower(trim(str_replace(' ', '-', $validated['name'])));

        // Double check after slugify
        if (Permission::where('name', $name)->exists()) {
            return redirect()->back()->with('error', "Permission '{$name}' already exists.");
        }

        $permission = Permission::create([
            'name' => $name,
            'guard_name' => 'web',
        ]);

        // Auto assign to Super Admin
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permission);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permissions.index')->with('success', "Permission '{$permission->name}' created successfully!");
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
        ]);

        $name = strtolower(trim(str_replace(' ', '-', $validated['name'])));

        $permission->name = $name;
        $permission->save();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permissions.index')->with('success', "Permission updated successfully!");
    }

    public function destroy(Permission $permission)
    {
        $name = $permission->name;
        $permission->roles()->detach();
        $permission->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permissions.index')->with('success', "Permission '{$name}' deleted successfully!");
    }
}
