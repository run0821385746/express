<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TransferDropCenter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transfer_dropcenter_booking_id', 
        'transfer_dropcenter_id',
        'transfer_dropcenter_status',
        'transfer_dropcenter_tracking_id',
        'transfer_dropcenter_sender_id',
        'transfer_bill_no_ref',
        'transfer_bill_id_ref',
        'transfer_dropcenter_tracking_no',
        'action_status',
        'parcel_received_amount',
        'to_dc_received_amount',
        'parcel_amount'
    ];

    public function dc_sender(){
        return $this->hasOne('App\Model\DropCenter','id','transfer_dropcenter_sender_id');
    }

    public function dc_receiver(){
        return $this->hasOne('App\Model\DropCenter','id','transfer_dropcenter_id');
    }

    public function tracking(){
        return $this->hasOne('App\Model\Tracking','id','transfer_dropcenter_tracking_id');
    }
    
    public function TransferDropCenterBill(){
        return $this->hasOne('App\Model\TransferDropCenterBill','id','transfer_bill_id_ref');
    }
}
