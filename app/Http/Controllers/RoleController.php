<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama role harus diisi',
            'name.unique' => 'Nama role sudah ada',
            'name.max' => 'Nama role maksimal 255 karakter',
        ]);

        $validated['guard_name'] = $validated['guard_name'] ?? 'web';

        Role::create($validated);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role baru berhasil ditambahkan');
    }

    /**
     * Show the form for editing role
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'guard_name' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama role harus diisi',
            'name.unique' => 'Nama role sudah ada',
            'name.max' => 'Nama role maksimal 255 karakter',
        ]);

        $validated['guard_name'] = $validated['guard_name'] ?? 'web';

        $role->update($validated);

        return redirect()
            ->route('roles.index')
            ->with('success', "Role '{$role->name}' berhasil diperbarui");
    }

    /**
     * Show the form for managing role permissions
     */
    public function permissions(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update role permissions
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Sync permissions to role
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', "Permission untuk role '{$role->name}' berhasil diperbarui");
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Tidak dapat menghapus role yang masih memiliki user');
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', "Role '{$roleName}' berhasil dihapus");
    }
}
