<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_name', 
        'product_width',
        'product_hight',
        'product_length',
        'product_dimension',
        'product_price_status',
        'product_price'
    ]; 
}
