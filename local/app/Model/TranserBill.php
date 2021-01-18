<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TranserBill extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transfer_bill_no', 
        'transfer_bill_courier_id',
        'transfer_bill_status',
        'tranfer_driver_sender_numberplate',
        'tranfer_by_employee_id',
        'tranfer_closing_by_employee_id',
        'tranfer_bill_branch_id',
    ];

    public function courier(){
        return $this->hasOne('App\Model\Employee','id','transfer_bill_courier_id');
    }

    public function employee_creeate(){
        return $this->hasOne('App\Model\Employee','id','tranfer_by_employee_id');
    }
    
    public function employee_closing(){
        return $this->hasOne('App\Model\Employee','id','tranfer_closing_by_employee_id');
    }
    
    // public function Customer(){
    //     return $this->hasOne('App\Model\Employee','id','tranfer_by_employee_id');
    // }
}
