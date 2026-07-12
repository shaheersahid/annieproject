<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): mixed
    {
        if (request()->ajax()) {
            $roles = Role::withCount('permissions')->get();
            return response()->json(['data' => $roles]);
        }
        return view('admin.content.roles.index');
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        Role::create(['name' => $request->validated('name'), 'guard_name' => 'web']);
        return redirect()->route('admin.users.roles.index')->with('success', 'Role created.');
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $role->update(['name' => $request->validated('name')]);
        return redirect()->route('admin.users.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();
        return redirect()->route('admin.users.roles.index')->with('success', 'Role deleted.');
    }

    public function editPermissions(Role $role): mixed
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return str($p->name)->before('.')->toString();
        });
        return view('admin.content.roles.permissions', compact('role', 'permissions'));
    }
}
