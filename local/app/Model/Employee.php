<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\DropCenter;

class Employee extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'emp_firstname',
        'emp_lastname',
        'emp_address',
        'emp_sub_district',
        'emp_district',
        'emp_province', 
        'emp_postcode',
        'emp_phone',
        'emp_position',
        'emp_status',
        'emp_branch_id',
        'emp_image',
        'courierstatus'
    ]; 

    public function dropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','emp_branch_id');
    }
} 