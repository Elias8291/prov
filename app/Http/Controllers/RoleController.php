<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        // Get all roles with their permissions
        $roles = Role::with(['permissions', 'users'])->paginate(10);
        $permissions = Permission::all();
        
        return view('roles.index', compact('roles', 'permissions'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
        ]);
        
        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Rol creado correctamente');
    }
    
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['nullable', 'array'],
        ]);
        
        $role->update(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->syncPermissions([]);
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado correctamente');
    }
    
    public function destroy(Role $role)
    {
        // Check if the role has users before deleting
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Este rol no puede ser eliminado porque tiene usuarios asignados');
        }
        
        $role->delete();
        
        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado correctamente');
    }
}