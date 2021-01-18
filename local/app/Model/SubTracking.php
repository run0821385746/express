<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubTracking extends Model
{
       /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subtracking_under_tracking_id', 
        'subtracking_no', 
        'subtracking_booking_id',
        'subtracking_tracking_id', 
        'subtracking_dimension_type', 
        'subtracking_cod',
        'subtracking_cod_fee',
        'subtracking_price',
        'subtracking_status',
        'subtracking_parcel_type'
    ];

    public function parceltype(){
        return $this->hasOne('App\Model\ParcelType','id','subtracking_parcel_type');
    }

    public function tracking() {
        return $this->hasOne('App\Model\Tracking','id','subtracking_tracking_id');
    }
    
    public function DimensionHistory() {
        return $this->hasOne('App\Model\DimensionHistory','dimension_history_subtracking_id','id');
    }
}
