<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubDistrict extends Model
{
       /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district_id',
        'sub_district_name',
        'postcode_id'
    ];

    public function district(){
        return $this->hasOne('App\Model\District','id','districe_id');
    }
}
