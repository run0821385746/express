<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class CourierCall extends Model
{
    protected $fillable = [
        'request_service_id',
        'tracking_id',
        'tranfer_id',
        'courier_id',
        'callstatus',
        'pick_time',
        'note',
        'oncall',
        'ontalk',
        'callTime'
    ];

    // public function employee(){
    //     return $this->hasOne('App\Model\Employee','id','employee_id');
    // }
}  
