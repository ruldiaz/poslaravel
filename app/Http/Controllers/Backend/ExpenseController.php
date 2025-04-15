<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function AddExpense() {
        return view('backend.expense.add_expense');
    } // end method
}
