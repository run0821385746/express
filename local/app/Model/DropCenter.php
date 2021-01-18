<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DropCenter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array  
     */
    protected $fillable = [
        'drop_center_name', 
        'drop_center_address', 
        'drop_center_sub_district', 
        'drop_center_district', 
        'drop_center_province', 
        'drop_center_postcode', 
        'drop_center_phone', 
        'drop_center_status',
        'drop_center_name_initial'
    ];

    public function District(){
        return $this->hasOne('App\Model\District','id','drop_center_sub_district');
    }
    public function amphure(){
        return $this->hasOne('App\Model\amphure','id','drop_center_district');
    }
    public function province(){
        return $this->hasOne('App\Model\province','id','drop_center_province');
    }
}
