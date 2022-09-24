<?php

namespace App\Imports;

use App\Models\Expense;
use App\Traits\Common;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExpensesImport implements ToModel, WithHeadingRow
{
    use Common;
    // use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Expense([
        'total_amount' => $row['amount'],
        'date' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])),
        'merchant' =>  $row['merchant'],
        'comment' => isset($row['comment']) ? $row['comment'] : null,
        'status' =>  $row['status'],
        'user_id' => auth()->user()->id,
        ]);
    }
}
