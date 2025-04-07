<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Intervention\Image\Facades\Image;

use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function EmployeeAttendanceList() {
        $allData = Attendance::select('date')->groupBy('date')->orderBy('date','desc')->get();
        return view('backend.attendance.view_employee_attend', compact('allData'));
    } // end method

    public function AddEmployeeAttendance() {
        $employees = Employee::all();
        return view('backend.attendance.add_employee_attend', compact('employees'));
    } // end method

    public function EmployeeAttendanceStore(Request $request) {
        $countemployee = count($request->employee_id);

        for($i = 0 ; $i<$countemployee ; $i++){
            $attend_status = 'attend_status'.$i;
            $attend = new Attendance();
            $attend->date = date('Y-m-d', strtotime($request->date));
            $attend->employee_id = $request->employee_id[$i];
            $attend->attend_status = $request->$attend_status;
            $attend->save();
        }

        $notification = array(
            'message' => 'Data Inserted Succesfully',
            'alert-type' => 'success'
        );

        return redirect()->route('employee.attend.list')->with($notification);
    } // end method
}
