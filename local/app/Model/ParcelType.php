<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ParcelType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parcel_type_name',
        'parcel_type_description',
        'parcel_type_status'
    ]; 
}
