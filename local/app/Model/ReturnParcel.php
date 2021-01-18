<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
  
class ReturnParcel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'return_tracking_id', 
        'tracking_no', 
        'parcel_receive', 
        'parcel_amount', 
        'from_courier_id',
        'to_drop_center_id', 
        'return_doing_by_employee_id', 
        'tranfer_bill_id',
        'return_status'
    ];

    public function Tracking(){
        return $this->hasOne('App\Model\Tracking','id','return_tracking_id');
    }

    public function courier(){
        return $this->hasOne('App\Model\Employee','id','from_courier_id');
    }
    
    public function DropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','to_drop_center_id');
    }

    public function employee(){
        return $this->hasOne('App\Model\Employee','id','return_doing_by_employee_id');
    }
    
    public function TranserBill(){
        return $this->hasOne('App\Model\TranserBill','id','tranfer_bill_id');
    }
}