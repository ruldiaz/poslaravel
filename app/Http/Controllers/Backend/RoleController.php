<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
}
