<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class CourierLogin extends Model
{
    protected $fillable = [
        'employee_id', 
        'login_status',
        'courier_login_image',
        'branch_id',
        'login_type',
        'lat_long'
    ];

    public function employee(){
        return $this->hasOne('App\Model\Employee','id','employee_id');
    }
}  
