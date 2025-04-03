<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvanceSalary;
use App\Models\Employee;
use Intervention\Image\Facades\Image;

use Carbon\Carbon;

class SalaryController extends Controller
{
    public function AddAvanceSalary() {
        $employee = Employee::latest()->get();
        return view('backend.salary.add_advance_salary', compact('employee'));
    } // end method

    public function AdvanceSalaryStore(Request $request) {
        $validateData = $request->validate([
            'month' => 'required',
            'year' => 'required',
            'advance_salary' => 'required|max:255',
        ]);

        $month = $request->month;
        $employee_id = $request->employee_id;

        $advanced = AdvanceSalary::where('month', $month)->where('employee_id', $employee_id)->first();
        if($advanced === NULL){
            AdvanceSalary::insert([
                'employee_id' => $request->employee_id,
                'month' => $request->month,
                'year' => $request->year,
                'advance_salary' => $request->advance_salary,
                'created_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Advance Salary Paid Succesfully',
                'alert-type' => 'success'
            );
    
            return redirect()->back()->with($notification);
        }else{
            $notification = array(
                'message' => 'Advance Already Paid',
                'alert-type' => 'warning'
            );
    
            return redirect()->back()->with($notification);
        }
    } // end method

    public function AllAvanceSalary(){
        $salary = AdvanceSalary::latest()->get();
        return view('backend.salary.all_advance_salary', compact('salary'));
    } // end method
}
