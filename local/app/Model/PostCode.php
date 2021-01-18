<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PostCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'postcode', 
        'drop_center_id'
    ];

    public function District(){
        return $this->hasOne('App\Model\District','zip_code','postcode');
    }

    public function DropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','drop_center_id');
    }
}
