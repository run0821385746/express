<?php

namespace App\Exports;

use App\Model\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Auth;
use App\Model\Employee;

class ExcelExportOrtherSale implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $user = Auth::user();
        $employee = Employee::find($user->employee_id);
        $date = date('Y-m-d');
        $sql = "SELECT b.product_name, a.sale_other_price, a.created_at FROM sale_others a left join product_prices b on a.sale_other_product_id = b.id where a.sale_other_branch_id = '$employee->emp_branch_id' and a.created_at like '$date%'";
        $bookmounts = DB::select($sql);
        return collect($bookmounts);
    }
}
