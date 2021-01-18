<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoginStartDay extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id', 
        'login_img',
        'login_lat_long',
        'login_time',
        'logout_img',
        'logout_lat_long',
        'logout_time',
        'branch_id'
    ];

    public function Employee(){
        return $this->hasOne('App\Model\Employee','id','employee_id');
    }
}
  