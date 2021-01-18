<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'zip_code', 
        'name_th',
        'amphure_id'
    ];

    public function postcode(){
        return $this->hasOne('App\Model\PostCode','id','postcode_id');
    }
    public function amphure(){
        return $this->hasOne('App\Model\amphure','id','amphure_id');
    }
}
  