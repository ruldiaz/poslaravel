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
        $allData = Attendance::orderBy('id','desc')->get();
        return view('backend.attendance.view_employee_attend', compact('allData'));
    } // end method

    public function AddEmployeeAttendance() {
        $employees = Employee::all();
        return view('backend.attendance.add_employee_attend', compact('employees'));
    } // end method
}
