<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class province extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 
        'name_th',
        'amphure_id'
    ];

    public function amphure(){
        return $this->hasOne('App\Model\amphure','province_id','id');
    }
}