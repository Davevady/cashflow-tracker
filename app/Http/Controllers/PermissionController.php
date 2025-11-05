<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index()
    {
        $permissions = Permission::orderBy('name')->paginate(20);

        return view('permissions.index', compact('permissions'));
    }

    /**
     * Store a newly created permission
     */
    public function createPermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama permission harus diisi',
            'name.unique' => 'Nama permission sudah ada',
            'name.max' => 'Nama permission maksimal 255 karakter',
        ]);

        $validated['guard_name'] = $validated['guard_name'] ?? 'web';

        Permission::create($validated);

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission baru berhasil ditambahkan');
    }

    /**
     * Remove the specified permission
     */
    public function destroyPermission(Permission $permission)
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return redirect()
                ->route('permissions.index')
                ->with('error', 'Tidak dapat menghapus permission yang masih digunakan oleh role');
        }

        $permissionName = $permission->name;
        $permission->delete();

        return redirect()
            ->route('permissions.index')
            ->with('success', "Permission '{$permissionName}' berhasil dihapus");
    }
}
