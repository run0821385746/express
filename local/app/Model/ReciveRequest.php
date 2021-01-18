<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class ReciveRequest extends Model
{
    protected $fillable = [
        'tracking_id', 
        'tracking_No',
        'parcel_amount',
        'recive_check',
        'recive_by',
        'branch_id',
        'from_courier',
        'recive_status',
        'booking_id'
    ];

    public function employee(){
        return $this->hasOne('App\Model\Employee','id','recive_by');
    }
    
    public function courier(){
        return $this->hasOne('App\Model\Employee','id','from_courier');
    }
}  
