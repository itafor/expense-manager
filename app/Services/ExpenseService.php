<?php
namespace App\Services;

use App\Models\Expense;
use App\Traits\FileUpload;
use App\Traits\Response;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class ExpenseService
{
    use Response, FileUpload;

    public function addExpense($data)
    {
        try {
            $expense = new Expense();
            $expense->user_id = auth()->user()->id;
            $expense->merchant = $data['merchant'];
            $expense->total_amount = $data['total_amount'];
            $expense->date = $data['date'];//Carbon::parse($this->formatDate($data['date'], 'm/d/Y', 'Y-m-d'));
            $expense->comment = isset($data['comment']) ? $data['comment'] : null;
            $expense->receipt_url = isset($data['receipt']) ? $this->uploadFile($data['receipt'])['full_path'] : null;
            $expense->save();

            return $this->success(false, "Expense successfully added!", $expense, 200);

        } catch (Exception $e) {
            return $this->fail(true, "Couldn't add expense!", $e->getMessage(), 400);
        }
    }

    public function updateExpense($data)
    {
        try {
            $expense = Expense::where([
                ['id', $data['expense_id']],
                ['user_id', auth()->user()->id]
            ])->first();

            if($expense){
            $expense->merchant = $data['merchant'];
            $expense->total_amount = $data['total_amount'];
            $expense->date = $data['date']; //Carbon::parse($this->formatDate($data['date'], 'm/d/Y', 'Y-m-d'));
            $expense->comment = isset($data['comment']) ? $data['comment'] :  $expense->comment;
            $expense->receipt_url = isset($data['receipt']) ? $this->uploadFile($data['receipt'])['full_path'] : $expense->receipt_url;
            $expense->status = isset($data['status']) ? $data['status'] : $expense->status;

            $expense->save();

            return $this->success(false, "Expense successfully updated!", $expense, 200);

            }

            return $this->fail(false, "Expense not found!", '', 200);

        } catch (Exception $e) {
            return $this->fail(true, "Couldn't update expense!", $e->getMessage(), 400);
        }
    }

    public function listExpense()
    {
        try {
            $expenses = Expense::with(['merchant'])->orderBy('created_at', 'desc')->get();

            return $this->success(false, "Expenses!", $expenses, 200);

        } catch (Exception $e) {
            return $this->fail(true, "Couldn't fetch expenses!", $e->getMessage(), 400);
        }
    }

    public function showExpense($expenseId)
    {
        try {
           $expense = Expense::where([
                ['id', $expenseId],
            ])->first();

            return $this->success(false, "Expense!", $expense, 200);

        } catch (Exception $e) {
            return $this->fail(true, "Couldn't fetch expense!", $e->getMessage(), 400);
        }
    }

    public function deleteExpense($expenseId)
    {
        try {
            $expense = Expense::where([
                ['id', $expenseId],
                ['user_id', auth()->user()->id]
            ])->first();

            if ($expense) {
                $expense->delete();
                return $this->success(false, "Expense deleted!", '', 200);

            }

            return $this->fail(true, "Expense not found!", '', 200);

        } catch (Exception $e) {
            return $this->fail(true, "Couldn't delete expense!", $e->getMessage(), 400);
        }
    }

     public function changeExpenseStatus($data)
    {
        try {
            $status = $data['status'];
            $expense = Expense::findOrFail($data['expense_id']);

            switch ($status) {
                case 'new':
                  $expense->status = 'New';
                  $expense->save();
            return $this->success(false, "Expense status successfully changed!", $expense, 200);
                    break;

                     case 'inprogress':
                  $expense->status = 'In Progress';
                  $expense->save();
            return $this->success(false, "Expense status successfully changed!", $expense, 200);
                    break;
             case 'reimburse':
                  $expense->status = 'Reimburse';
                  $expense->save();
             return $this->success(false, "Expense status successfully changed!", $expense, 200);
                    break;
                
                default:
            return $this->fail(true, "Invalid expense status $status!", '', 400);
                    break;
            }

        } catch (Exception $e) {
            return $this->fail(true, "Couldn't changed expense status!", $e->getMessage(), 400);
        }
    }
   
   public function formatDate($date, $oldFormat, $newFormat)
    {
        return Carbon::createFromFormat($oldFormat, $date)->format($newFormat);
    }


     public function filterExpense($data)
    {
        try {

            $from = isset($data['from']) ? $data['from'] : "";
            $to = isset($data['to']) ? $data['to'] : "";

            $maxAmount = isset($data['max_amount']) ? $data['max_amount'] : "";
            $minAmount = isset($data['min_amount']) ? $data['min_amount'] : "";
            $statuses =  isset($data['status']) ? $data['status'] : [];
            $merchant = isset($data['merchant']) ? $data['merchant'] : "";

            $query = DB::table('expenses'); //->join('merchants','expenses.merchant_id','=','merchants.id');

            if($merchant)
            {
                $query->where('merchant', $merchant);
            }

             if($from && $to){
            $query->whereBetween('date', [$from, $to]);
            }elseif($from && !$to){
            $query->where('date', '>=', $from);
            }

            if($maxAmount && $minAmount){
            $query->whereBetween('total_amount', [$minAmount, $maxAmount]);
            }elseif($minAmount && !$maxAmount){
            $query->where('total_amount', '>=', $minAmount);
            }

            $query->whereIn('status', $statuses);
            
            $expenses = $query->orderBy('created_at', 'desc')->get()->all();

            return $this->success(false, "Expenses!", $expenses, 200);

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "something went wrong: $error!", '', 400);
        }
    }

    
}
