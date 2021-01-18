<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TrackCustomer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'track_id', 
        'tracking_no', 
        'tracking_status', 
        'setwrongs_time', 
        'created_at',
        'cus_id',
        'cust_name',
        'cust_address',
        'cust_sub_district',
        'sub_district_name',
        'cust_district',
        'district_name',
        'cust_province',
        'province_name',
        'cust_postcode',
        'dropcenters_recive',
        'dropcenter_to',
    ];

    // public function District(){
    //     return $this->hasOne('App\Model\District','zip_code','postcode');
    // }
}
