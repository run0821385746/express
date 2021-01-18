<?php
  
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerCod extends Model
{
    protected $table = "customer_cods";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 
        'cust_phone', 
        'cust_mail',
        'cust_bookbank_name', 
        'cust_id_card', 
        'cust_bank_no', 
        'cust_bank_name',
        'cust_billing_address', 
        'cust_sub_district',
        'cust_district',
        'cust_province',
        'cust_postcode',
        'cust_idcard_front_img',
        'cust_idcard_back_img',
        'cust_bookbank_img',
        'cust_sign_contract_img',
        'update_by',
        'cod_status'
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

}
