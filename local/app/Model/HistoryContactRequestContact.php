<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Booking;
use App\Model\Tracking;
use App\Model\Employee;
use App\Model\DropCenter;

class HistoryContactRequestContact extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'history_booking_id',
        'history_currier_id',
        'history_reason_id',
        'history_reason_detail',
        'history_timing_call',
        'history_status',
        'history_branch_id'
    ]; 
    
    public function booking(){
        return $this->hasOne('App\Model\Booking','id','history_booking_id');
    }
    public function tracking(){
        return $this->hasOne('App\Model\Tracking','id','history_tracking_id');
    }
    public function currier(){
        return $this->hasOne('App\Model\Employee','id','history_currier_id');
    }
    public function dropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','history_branch_id');
    }
}
