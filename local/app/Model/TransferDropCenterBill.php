<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TransferDropCenterBill extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'transfer_bill_no', 
        'transfer_sender_id',
        'transfer_recriver_id',
        'transfer_bill_status',
        'tranfer_driver_sender_name',
        'tranfer_driver_sender_numberplate',
        'tranfer_driver_sender_phone',
        'tranfer_employee_sender_id',
        'tranfer_employee_recive_id'
    ];

    public function dc_sender(){
        return $this->hasOne('App\Model\DropCenter','id','transfer_sender_id');
    }

    public function dc_receiver(){
        return $this->hasOne('App\Model\DropCenter','id','transfer_recriver_id');
    }

    public function Employee(){
        return $this->hasOne('App\Model\Employee','id','tranfer_employee_recive_id');
    }
    
    public function Employee_sender(){
        return $this->hasOne('App\Model\Employee','id','tranfer_employee_sender_id');
    }
    
    public function Employee_driver(){
        return $this->hasOne('App\Model\Employee','id','tranfer_driver_sender_name');
    }
}
