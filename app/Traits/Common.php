<?php
namespace App\Traits;

use Carbon\Carbon;
use Exception;

trait Common 
{

    public function formatDate($date, $oldFormat, $newFormat)
    {
        return Carbon::createFromFormat($oldFormat, $date)->format($newFormat);
    }
   
}