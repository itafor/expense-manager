<?php

namespace App\Http\Controllers;

use App\Imports\ExpensesImport;
use App\Models\Expense;
use App\Services\ExpenseService;
use App\Traits\Response;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseController extends Controller
{
	use Response;
    public $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

      public function addExpense(Request $request)
    {
    	$validated = $request->validate([
    		"merchant" => "required|string",
    		"total_amount" => "required|numeric",
    		"date" => "required|string",
    		"comment" => "nullable",
    		"receipt" => "nullable",
    	]);

    	if(isset($validated['receipt'])){
             $request->validate([
            "receipt" => 'required|file|mimes:jpg,jpeg,png|max:5540',
        ]);
        }

        return $this->expenseService->addExpense($validated);
    }

      public function updateExpense(Request $request)
    {
        $validated = $request->validate([
            "expense_id" => "required|numeric|exists:expenses,id",
            "merchant" => "required",
            "total_amount" => "required|numeric",
            "date" => "required|string",
            "comment" => "nullable",
            "receipt" => "nullable",
            "status" => "nullable",
        ]);

        if(isset($validated['receipt'])){
             $request->validate([
            "receipt" => 'required|file|mimes:jpg,jpeg,png|max:5540',
        ]);
        }

        return $this->expenseService->updateExpense($validated);
    }

      public function showExpense($expenseId)
    {
        return $this->expenseService->showExpense($expenseId);
    }

      public function deleteExpense($expenseId)
    {
        return $this->expenseService->deleteExpense($expenseId);
    }

     public function changeExpenseStatus(Request $request)
    {
    	$validated = $request->validate([
            "expense_id" => "required|numeric|exists:expenses,id",
    		"status" => "required|string",
    	]);

        return $this->expenseService->changeExpenseStatus($validated);
    }

     public function addMerchant(Request $request)
    {
    	$validated = $request->validate([
            "name" => "required|string",
    	]);

        return $this->expenseService->addMerchant($validated);
    }

     public function filterExpense(Request $request)
    {
    	$validated = $request->validate([
    		"merchant" => "nullable",
    		"from" => "nullable",
    		"to" => "nullable",
    		"max_amount" => "nullable",
    		"min_amount" => "nullable",
    		"status" => "nullable",
    	]);

        return $this->expenseService->filterExpense($validated);
    }

     public function importExpenses(Request $request)
    {
    	try {

    	 $request->validate([
    		  'expenses' => 'required|file|mimes:xls,xlsx,csv|max:5540'
    	]);

    	 Excel::import(new ExpensesImport,request()->file('expenses')->store('temp'));
        return $this->success(false, "Expenses successfully imported !", '', 200);

        } catch (Exception $e) {
            return $this->fail(true, "Couldn't import expenses!", $e->getMessage(), 400);
        }
    }

      public function sumExpensesToReimburse()
    {
        try {

        
             $expenses = Expense::where([
                ['status', '!=', 'Reimburse'],
            ])->get();

             $expensesToReimburse =  round($expenses->sum('total_amount'),2);
        
        return $this->success(false, "Expenses to Reimburse !", $expensesToReimburse, 200);

        } catch (Exception $e) {
            return $this->fail(true, "Couldn't display expenses to reimburse!", $e->getMessage(), 400);
        }
    }

    
}
