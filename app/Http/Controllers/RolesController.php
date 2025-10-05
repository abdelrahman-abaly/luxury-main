<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RolesController extends Controller
{
    protected $tables;
    protected $abilities;
    protected $permissions;
    public function __construct() {
        $this->tables = DB::table("system_tables")->pluck("name")->toArray();
        $this->abilities = ['view', 'create', 'edit', 'delete'];
        $this->permissions = Permission::all()->groupBy('table_name');
    }


    public function index() {
        $roles = Role::all();

        return view('roles.index', compact('roles'));
    }

    public function create(Request $request) {
        return view('roles.create', array('tables' => $this->tables,
            'abilities' => $this->abilities,
                'permissions' => $this->permissions)
        );
    }

    public function store(Request $request) {
        $role_name = $request->input('role_name');
        $role_code = $request->input('role_code');
        $permissions = $request->input('permissions');

        try {
            $role = new Role();

            $role->role_name = $role_name;
            $role->role_code = $role_code;
            $role->save();

            foreach ($permissions as $permission) {
                DB::table("permission_role")->insert(['role_id' => $role->id, 'permission_id' => $permission]);
            }



            return redirect()->route('roles.index')->with('success', 'Role Created!');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function edit($id) {
        $role_id = $id;

        $role = Role::where('id', $role_id)->first();

        return view('roles.edit', array('tables' => $this->tables,
                'abilities' => $this->abilities,
                'permissions' => $this->permissions,
                'role' => $role)
        );
    }

    public function update(Request $request) {
        $role_id = $request->input('role_id');
        $role_name = $request->input('role_name');
        $role_code = $request->input('role_code');
        $permissions = $request->input('permissions');


        try {
            DB::table("roles")->where('id', $role_id)->update(['role_name' => $role_name, 'role_code' => $role_code]);

            foreach ($permissions as $permission) {
                if(DB::table("permission_role")->where('role_id', $role_id)->where('permission_id', $permission)->exists()) {
                    continue;
                } else {
                    DB::table("permission_role")->insert(['role_id' => $role_id, 'permission_id' => $permission]);
                }
            }



            return redirect()->route('roles.index')->with('success', 'Role updated!');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Request $request) {
        $role_id = $request->input('role_id');
        $role = Role::where('id', $role_id)->first();

        // Prevent deletion of admin role
        if ($role->role_code === 'super-admin') {
            return redirect()->back()
                ->with('error', 'Cannot delete the Super Admin role!');
        }

        // Check if role has users assigned
        if ($role->users()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete role with assigned users!');
        }

        $role->delete();
        DB::table("permission_role")->where('role_id', $role_id)->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }
}
