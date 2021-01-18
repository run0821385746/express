<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SaleOther extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sale_other_product_id',
        'sale_other_price',
        'sale_other_branch_id',
        'sale_other_booking_id',
        'sale_other_tr_id'
    ]; 

    public function productPrice(){
        return $this->hasOne('App\Model\ProductPrice','id','sale_other_product_id');
    }
    
    public function Booking(){
        return $this->hasOne('App\Model\Booking','id','sale_other_booking_id');
    }

    public function DropCenter(){
        return $this->hasOne('App\Model\DropCenter','id','sale_other_branch_id');
    }
}
