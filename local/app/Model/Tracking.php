<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
       /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tracking_no', 
        'tracking_booking_id',
        'tracking_receiver_id', 
        'tracking_parcel_type', 
        'parcel_return_amount', 
        'tracking_status',
        'tracking_amount',
        'tracking_send_status',
        'send_pick_time',
        'orther_dc_revice_time',
        'tracking_note'
    ];

    public function receiver(){
        return $this->hasOne('App\Model\Customer','id','tracking_receiver_id');
    }

    public function customer(){
        return $this->hasOne('App\Model\Customer','id','tracking_receiver_id');
    }

    public function booking(){
        return $this->hasOne('App\Model\Booking','id','tracking_booking_id');
    }

    public function Con(){
        return $this->hasOne('App\Model\Con','track_id','id');
    }

    // public function SubTracking(){
    //     return $this->belongsToMany('App\Model\SubTracking','subtracking_tracking_id');
    // }
}
