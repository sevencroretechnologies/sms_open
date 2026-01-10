<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(Request $request)
    {
        $query = Permission::query()->where('guard_name', 'web');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Module filter
        if ($request->filled('module')) {
            $query->where('name', 'like', $request->module . '.%');
        }

        $permissions = $query->orderBy('name')->paginate(20);

        // Get unique modules for filter dropdown
        $modules = Permission::where('guard_name', 'web')
            ->get()
            ->map(function ($permission) {
                return explode('.', $permission->name)[0];
            })
            ->unique()
            ->sort()
            ->values();

        // Group permissions by module for display
        $groupedPermissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        return view('admin.permissions.index', compact('permissions', 'modules', 'groupedPermissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        // Get existing modules for suggestions
        $modules = Permission::where('guard_name', 'web')
            ->get()
            ->map(function ($permission) {
                return explode('.', $permission->name)[0];
            })
            ->unique()
            ->sort()
            ->values();

        return view('admin.permissions.create', compact('modules'));
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name', 'regex:/^[a-z]+\.[a-z]+$/'],
        ], [
            'name.regex' => 'Permission name must be in format: module.action (e.g., students.view)',
        ]);

        try {
            Permission::create([
                'name' => strtolower($validated['name']),
                'guard_name' => 'web',
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create permission: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified permission.
     */
    public function show($id)
    {
        $permission = Permission::where('guard_name', 'web')->findOrFail($id);
        
        // Get roles that have this permission
        $roles = Role::whereHas('permissions', function ($query) use ($permission) {
            $query->where('permissions.id', $permission->id);
        })->get();

        return view('admin.permissions.show', compact('permission', 'roles'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit($id)
    {
        $permission = Permission::where('guard_name', 'web')->findOrFail($id);
        
        $modules = Permission::where('guard_name', 'web')
            ->get()
            ->map(function ($permission) {
                return explode('.', $permission->name)[0];
            })
            ->unique()
            ->sort()
            ->values();

        return view('admin.permissions.create', compact('permission', 'modules'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::where('guard_name', 'web')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($permission->id), 'regex:/^[a-z]+\.[a-z]+$/'],
        ], [
            'name.regex' => 'Permission name must be in format: module.action (e.g., students.view)',
        ]);

        try {
            $permission->update([
                'name' => strtolower($validated['name']),
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update permission: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy($id)
    {
        $permission = Permission::where('guard_name', 'web')->findOrFail($id);

        try {
            // Remove permission from all roles first
            $permission->roles()->detach();
            $permission->delete();

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete permission: ' . $e->getMessage());
        }
    }

    /**
     * Assign permission to roles.
     */
    public function assignToRoles(Request $request, $id)
    {
        $permission = Permission::where('guard_name', 'web')->findOrFail($id);

        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        try {
            // Get role names from IDs
            $roleIds = $validated['roles'] ?? [];
            $roles = Role::whereIn('id', $roleIds)->pluck('name')->toArray();

            // Sync roles for this permission
            $permission->syncRoles($roles);

            return redirect()->route('admin.permissions.show', $permission->id)
                ->with('success', 'Roles updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign roles: ' . $e->getMessage());
        }
    }
}
