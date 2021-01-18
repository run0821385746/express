<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DimensionHistory extends Model
{
       /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dimension_history_tracking_id',
        'dimension_history_subtracking_id',
        'dimension_history_width',
        'dimension_history_hight',
        'dimension_history_length',
        'dimension_history_total_dimension',
        'dimension_history_weigth',
        'dimension_history_status'
    ];

    public function subtracking() {
        return $this->hasOne('App\Model\SubTracking','id','dimension_history_subtracking_id');
    }
}
