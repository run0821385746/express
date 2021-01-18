<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class PacelCareStatus extends Model
{
    public $table = "pacel_care_statuss";

    protected $fillable = [
        'status'
    ];

    // public function employee(){
    //     return $this->hasOne('App\Model\Employee','id','employee_id');
    // }
}  
