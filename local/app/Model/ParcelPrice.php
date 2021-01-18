<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ParcelPrice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parcel_total_dimension',
        'parcel_total_weight',
        'parcel_price',
        'parcel_price_status'
    ]; 
}
