<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
  
class Booking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_no', 
        'booking_branch_id',
        'booking_sender_id', 
        'booking_type', 
        'booking_status',
        'create_by',
        'booking_amount',
        'receive_money'
    ];

    public function customer(){
        return $this->hasOne('App\Model\Customer','id','booking_sender_id');
    }
    
    public function RequestService(){
        return $this->hasOne('App\Model\RequestService','request_booking_id','id');
    }
    
    public function DropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','booking_branch_id');
    }
    
    public function Employee(){
        return $this->hasOne('App\Model\Employee','id','create_by');
    }
}