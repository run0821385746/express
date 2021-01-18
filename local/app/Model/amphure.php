<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class amphure extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 
        'name_th',
        'province_id'
    ];

    public function District(){
        return $this->hasOne('App\Model\District','amphure_id','id');
    }
    
    public function province(){
        return $this->hasOne('App\Model\province','id','province_id');
    }

}