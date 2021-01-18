<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OwnerDashboard extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'drop_center_id', 
        'cls',
        'cons',
        'pod',
        'dly',
        'cod',
        'cod_all',
        'lh',
        'on_lh',
        'dvl',
        'tranfer_bill'
    ];

    public function DropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','drop_center_id');
    }
}
