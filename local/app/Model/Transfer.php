<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transfer_booking_id', 
        'transfer_courier_id',
        'transfer_status',
        'transfer_tracking_id',
        'parcel_received_amount',
        'parcel_amount',
        'photo',
        'signature',
        'receive_name',
        'receive_relation',
        'cod_amount',
        'transfer_bill_id',
        'transfer_branch_id',
        'count_call',
        'recive_admit'
    ];

    public function tracking(){
        return $this->hasOne('App\Model\Tracking','id','transfer_tracking_id');
    }

    public function employee(){
        return $this->hasOne('App\Model\Employee','id','transfer_courier_id');
    }

    public function booking(){
        return $this->hasOne('App\Model\Booking','id','transfer_booking_id');
    }

    public function TranserBill(){
        return $this->hasOne('App\Model\TranserBill','id','transfer_bill_id');
    }

    public function DropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','transfer_branch_id');
    }
}
