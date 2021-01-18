<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Employee;
use App\Model\DropCenter;

class HistoryContact extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'history_tracking_id',
        'history_currier_id',
        'history_reason_id',
        'history_timing_call',
        'history_status',
        'history_branch_id'
    ]; 

    public function booking(){
        return $this->hasOne('App\Model\Empoloyee','id','history_currier_id');
    }

    public function dropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','history_branch_id');
    }
}
