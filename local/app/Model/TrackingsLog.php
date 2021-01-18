<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
  
class TrackingsLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;

    protected $fillable = [
        'tracking_no', 
        'tracking_receiver_id',
        'tracking_status_id', 
        'tracking_branch_id_dc', 
        'tracking_branch_id_sub_dc',
        'tracking_date',
        'tracking_cause'
    ];
}