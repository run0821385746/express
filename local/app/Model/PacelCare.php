<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class PacelCare extends Model
{
    protected $fillable = [
        'tracking_id',
        'doing_by',
        'branch_id',
        'status',
        'ref_no'
    ];

    public function PacelCareStatus(){
        return $this->hasOne('App\Model\PacelCareStatus','id','status');
    }
    
    public function Employee(){
        return $this->hasOne('App\Model\Employee','id','doing_by');
    }
    
    public function DropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','branch_id');
    }
    
    public function Tracking(){
        return $this->hasOne('App\Model\Tracking','id','tracking_id');
    }
}  
