<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function AddExpense() {
        return view('backend.expense.add_expense');
    } // end method

    public function StoreExpense(Request $request) {
        Expense::insert([
            'details' => $request->details,
            'amount' => $request->amount,
            'month' => $request->month,
            'year' => $request->year,
            'date' => $request->date,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Expense Inserted Succesfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } // end method

    public function TodayExpense() {
        $date = date("d-m-Y");
        $today = Expense::where('date', $date)->get();
        return view('backend.expense.today_expense', compact('today'));
    } // end method

    public function EditExpense($id) {
        $expense = Expense::findOrFail($id);
        return view('backend.expense.edit_expense', compact('expense'));
    } // end method

    public function UpdateExpense(Request $request) {
        $expense_id = $request->id;
        Expense::findOrFail($expense_id)->update([
            'details' => $request->details,
            'amount' => $request->amount,
            'month' => $request->month,
            'year' => $request->year,
            'date' => $request->date,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Expense Updated Succesfully',
            'alert-type' => 'success'
        );

        return redirect()->route('today.expense')->with($notification);
    } // end method

    public function MonthExpense() {
        $month = date("F");
        $monthExpense = Expense::where('month', $month)->get();
        return view('backend.expense.month_expense', compact('monthExpense'));
    }  // end method

    public function YearExpense() {
        $year = date("Y");
        $yearExpense = Expense::where('year', $year)->get();
        return view('backend.expense.year_expense', compact('yearExpense'));
    } // end method
}
