<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Employee;
use App\Model\DropCenter;
use App\Model\Tracking;

class ParcelReceive extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'receiver_tracking_id',
        'receiver_image',        
        'receiver_signatur',        
        'receiver_name',
        'receiver_type_id',
        'receiver_other_type_name',        
        'receiver_branch_id',
        'receiver_currier_id',
        'receiver_status'          
    ]; 

    public function tracking(){
        return $this->hasOne('App\Model\Tracking','id','receiver_tracking_id');
    }

    public function employee(){
        return $this->hasOne('App\Model\Employee','id','receiver_currier_id');
    }

    public function branch(){
        return $this->hasOne('App\Model\DropCenter','id','receiver_branch_id');
    }
}
