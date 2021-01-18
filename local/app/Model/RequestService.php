<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
  
class RequestService extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array  
     */
    protected $fillable = [
        'request_booking_id', 
        'request_sender_id', 
        'request_currier_id', 
        'request_status',
        'branch_id',
        'request_parcel_qty',
        'action_status',
        'request_booking_no'
    ]; 
  
    public function sender(){
        return $this->hasOne('App\Model\Customer','id','request_sender_id');
    }

    public function booking(){
        return $this->hasOne('App\Model\Booking','id','request_booking_id');
    }

    public function courier(){
        return $this->hasOne('App\Model\Employee','id','request_currier_id');
    }
}