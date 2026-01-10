<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * System roles that cannot be deleted
     */
    protected array $systemRoles = ['admin', 'teacher', 'student', 'parent', 'accountant', 'librarian'];

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $query = Role::query()->where('guard_name', 'web');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $query->withCount(['users', 'permissions'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => strtolower(str_replace(' ', '-', $validated['name'])),
                'display_name' => $validated['display_name'] ?? $validated['name'],
                'description' => $validated['description'] ?? null,
                'guard_name' => 'web',
            ]);

            if (!empty($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified role.
     */
    public function show($id)
    {
        $role = Role::where('guard_name', 'web')
            ->withCount(['users', 'permissions'])
            ->findOrFail($id);

        $users = $role->users()->paginate(10);
        $permissions = $role->permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        $statistics = [
            'users_count' => $role->users_count,
            'permissions_count' => $role->permissions_count,
        ];

        return view('admin.roles.show', compact('role', 'users', 'permissions', 'statistics'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($id)
    {
        $role = Role::where('guard_name', 'web')->findOrFail($id);
        
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.create', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::where('guard_name', 'web')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'display_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        // Prevent renaming system roles
        if (in_array($role->name, $this->systemRoles) && $validated['name'] !== $role->name) {
            return back()->withInput()
                ->with('error', 'System roles cannot be renamed.');
        }

        try {
            DB::beginTransaction();

            $role->update([
                'name' => in_array($role->name, $this->systemRoles) ? $role->name : strtolower(str_replace(' ', '-', $validated['name'])),
                'display_name' => $validated['display_name'] ?? $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            if (isset($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy($id)
    {
        $role = Role::where('guard_name', 'web')->findOrFail($id);

        // Prevent deletion of system roles
        if (in_array($role->name, $this->systemRoles)) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users. Please reassign users first.');
        }

        try {
            $role->delete();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Show the permissions management page for a role.
     */
    public function permissions($id)
    {
        $role = Role::where('guard_name', 'web')->findOrFail($id);
        
        $allPermissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.permissions', compact('role', 'allPermissions', 'rolePermissions'));
    }

    /**
     * Update the permissions for a role.
     */
    public function updatePermissions(Request $request, $id)
    {
        $role = Role::where('guard_name', 'web')->findOrFail($id);

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        try {
            $role->syncPermissions($validated['permissions'] ?? []);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Permissions updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update permissions: ' . $e->getMessage());
        }
    }
}
