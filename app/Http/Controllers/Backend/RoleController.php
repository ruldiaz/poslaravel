<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function AllPermission() {
        $permissions = Permission::all();
    return view('backend.pages.permission.all_permission', compact('permissions'));
    } // End method

    public function AddPermission() {
        return view('backend.pages.permission.add_permission');
    } // End method

    public function StorePermission(Request $request) {
        $role = Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        $notification = array(
            'message' => 'Permission Added Succesfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.permission')->with($notification);
    } // End method

    public function EditPermission($id) {
        $permission = Permission::findOrFail($id);
        return view('backend.pages.permission.edit_permission', compact('permission'));
    } // End mehod

    public function UpdatePermission(Request $request) {
        $per_id = $request->id;

        Permission::findOrFail($per_id)->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        $notification = array(
            'message' => 'Permission Updated Succesfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.permission')->with($notification);
    } // End method

    public function DeletePermission($id) {
        Permission::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Permission Deleted Succesfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End method

    // All Roles Methods //

    public function AllRoles() {
        $roles = Role::all();
        return view('backend.pages.roles.all_roles', compact('roles'));
    }  // End Method

    public function AddRoles() {
        return view('backend.pages.roles.add_roles');
    }  // End method

    public function StoreRoles(Request $request) {
        $role = Role::create([
            'name' => $request->name,
        ]);

        $notification = array(
            'message' => 'Role Added Succesfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles')->with($notification);
    }  // End method

    public function EditRoles($id) {
        $roles = Role::findOrFail($id);
        return view('backend.pages.roles.edit_roles', compact('roles'));
    }  // End method

    public function UpdateRoles(Request $request) {
        $role_id = $request->id;

        Role::findOrFail($role_id)->update([
            'name' => $request->name,
        ]);

        $notification = array(
            'message' => 'Role Updated Succesfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles')->with($notification);
    }  // End method

    public function DeleteRoles($id) {
        Role::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Role Deleted Succesfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }  // End method


    // Add Roles Permission All Methods

    public function AddRolesPermission() {
        $roles = Role::all();
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view('backend.pages.roles.add_roles_permission', compact('roles','permissions','permission_groups'));
    }  // End method

    public function StoreRolesPermission(Request $request) {
        $data = array();
        $permissions = $request->permission;

        foreach($permissions as $key => $item){
            $data['role_id'] = $request->role_id;
            $data['permission_id'] = $item;

            DB::table('role_has_permissions')->insert($data);
        }

         $notification = array(
            'message' => 'Role Permission Added Succesfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles.permission')->with($notification);
    } // End method

    public function AllRolesPermission() {
        $roles = Role::all();

        return view('backend.pages.roles.all_roles_permission', compact('roles'));
    }  // End method

    public function AdminEditRoles($id) {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view('backend.pages.roles.edit_roles_permission', compact('role','permissions','permission_groups'));
    }  // End method

   public function RolePermissionUpdate(Request $request, $id) {
    $role = Role::findOrFail($id);
    $permissionIds = $request->permission;

    if (!empty($permissionIds)) {
        // Get the actual permission models
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        
        // Sync using the permission objects
        $role->syncPermissions($permissions);
    } else {
        // If no permissions selected, remove all permissions
        $role->syncPermissions([]);
    }

    $notification = array(
        'message' => 'Role Permission Updated Successfully',
        'alert-type' => 'success'
    );
    return redirect()->route('all.roles.permission')->with($notification);
}
}
