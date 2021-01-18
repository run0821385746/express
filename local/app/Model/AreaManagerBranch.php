<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class AreaManagerBranch extends Model
{
    protected $table = 'area_manager_branchs';
    protected $fillable = [
        'employee_id', 
        'branch_id', 
        'create_by'
    ];

    public function employee(){
        return $this->hasOne('App\Model\Employee','id','employee_id');
    }
    public function create(){
        return $this->hasOne('App\Model\Employee','id','create_by');
    }
    public function DropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','branch_id');
    }
}  
