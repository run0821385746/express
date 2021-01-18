<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'emp_id', 
        'daily_summaries_menu', 
        'parcel_care_menu', 
        'receive_parcel_menu', 
        'all_parcel_menu', 
        'parcel_cls_menu', 
        'parcel_send_menu', 
        'parcel_call_recive_menu', 
        'recive_parcel_from_dc_menu', 
        'orther_report_menu',
        'customer_menu',
        'employ_menu',
        'permiss_menu',
        'dropcenter_menu',
        'orther_sale_menu',
        'service_price_menu',
        'parcel_type_menu',
        'permission_status', 
        'branch_id', 
        'update_by'
    ];

    public function employee(){
        return $this->hasOne('App\Model\Employee','id','emp_id');
    }
}
  