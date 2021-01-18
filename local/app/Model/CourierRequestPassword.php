<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class CourierRequestPassword extends Model
{
    protected $fillable = [
        'employee_id', 
        'emp_branch_id', 
        'status'
    ];

    public function employee(){
        return $this->hasOne('App\Model\Employee','id','employee_id');
    }
}  
