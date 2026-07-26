<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    private const PROTECTED_ROLES = ['admin', 'user'];

    public function __construct(private AuditLogger $auditLogger) {}

    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($request->permissions ?? []);
        $this->auditLogger->log('role.created', $role, "Created role: {$role->name}", [], [
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect('/admin/roles')->with('success', __('messages.create_success'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $oldValues = [
            'name' => $role->name,
            'permissions' => $role->permissions()->pluck('name')->all(),
        ];
        $permissions = $request->permissions ?? [];

        $role->update(['name' => $request->name]);
        $role->syncPermissions($permissions);
        $this->auditLogger->log('role.updated', $role, "Updated role: {$role->name}", $oldValues, [
            'name' => $role->name,
            'permissions' => $permissions,
        ]);

        return redirect('/admin/roles')->with('success', __('messages.update_success'));
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return back()->with('error', __('messages.cannot_delete_system_role'));
        }

        $this->auditLogger->log('role.deleted', $role, "Deleted role: {$role->name}", [
            'name' => $role->name,
            'permissions' => $role->permissions()->pluck('name')->all(),
        ]);
        $role->delete();

        return redirect('/admin/roles')->with('success', __('messages.delete_success'));
    }
}
