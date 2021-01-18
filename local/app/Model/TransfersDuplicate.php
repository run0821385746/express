<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TransfersDuplicate extends Model
{
       /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'duplicate_tracking_no', 
        'duplicate_courier_id',
        'duplicate_status'
    ];

    // public function receiver(){
    //     return $this->hasOne('App\Model\Customer','id','tracking_receiver_id');
    // }

    // public function customer(){
    //     return $this->hasOne('App\Model\Customer','id','tracking_receiver_id');
    // }

    // public function booking(){
    //     return $this->hasOne('App\Model\Booking','id','tracking_booking_id');
    // }
}
