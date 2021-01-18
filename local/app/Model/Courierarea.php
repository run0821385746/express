<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class CourierArea extends Authenticatable
{
    protected $fillable = [
        'post_code_id', 
        'employee_id'
    ];

    public function employee(){
        return $this->hasOne('App\Model\Employee','id','employee_id');
    }
}  
