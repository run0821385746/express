<?php

namespace App\Exports;

use App\Model\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Auth;
use App\Model\Employee;

class ExcelExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Booking::all();
    }
}
