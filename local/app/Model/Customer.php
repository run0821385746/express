<?php
  
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cust_name', 
        'cust_address',
        'cust_sub_district', 
        'cust_district', 
        'cust_province', 
        'cust_postcode',
        'cust_phone', 
        'cust_status',
        'cust_cod_register_status'
    ];

    public function District(){
        return $this->hasOne('App\Model\District','id','cust_sub_district');
    }
    public function amphure(){
        return $this->hasOne('App\Model\amphure','id','cust_district');
    }
    public function province(){
        return $this->hasOne('App\Model\province','id','cust_province');
    }
    
    public function CustomerCod(){
        return $this->hasOne('App\Model\CustomerCod','id','cust_cod_register_status');
    }
    
    public function PostCode(){
        return $this->hasOne('App\Model\PostCode','postcode','cust_postcode');
    }
}
