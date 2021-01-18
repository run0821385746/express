<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TrackingClearDay extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tracking_id', 
        'note'
    ];

    public function Tracking(){
        return $this->hasOne('App\Model\Tracking','id','tracking_id');
    }
}
