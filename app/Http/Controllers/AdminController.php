<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    //
    public function AdminDestroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'Admin Logout Succesfully',
            'alert-type' => 'info'
        );

        return redirect('/logout')->with($notification);
    }//end method

    public function AdminLogoutPage() {
        return view('admin.admin_logout');
    }//end method

    public function AdminProfile() {
        $id = Auth::user()->id;
        $adminData = User::find($id);
        return view('admin.admin_profile_view', compact('adminData'));
    }// end method

    public function AdminProfileStore(Request $request) {
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;

        if($request->file('photo')){
            $file = $request->file('photo');
            @unlink(public_path('upload/admin_image/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/admin_image'), $filename);
            $data['photo'] = $filename;
        }

            $data->save();

            $notification = array(
                'message' => 'Admin Profile Updated Succesfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);
    }//end method

    public function ChangePassword() {
        return view('admin.change_password');
    } // end method

    public function UpdatePassword(Request $request) {
        // Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        // Match the old password
        if(!Hash::check($request->old_password, auth::user()->password)){

            $notification = array(
                'message' => 'Old Password Does Not Match!',
                'alert-type' => 'error'
            );

            return back()->with($notification);
        }

        User::whereId(Auth::user()->id)->update([
         'password' => Hash::make($request->new_password)
         ]);

        $notification = array(
            'message' => 'Password Updated Succesfully',
            'alert-type' => 'success'
        );

        return back()->with($notification);

    }//end method

    // Admin User All Method //
    public function AllAdmin() {
        $alladminuser = User::latest()->get();
        return view('backend.admin.all_admin', compact('alladminuser'));
    } // End Method

    public function AddAdmin() {
        $roles = Role::all();

        return view('backend.admin.add_admin', compact('roles'));
    } // End method

}
