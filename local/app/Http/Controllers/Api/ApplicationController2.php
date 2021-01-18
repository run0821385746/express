<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Admin;
use App\Model\User;
use App\Model\Employee;
use App\Model\Tracking;
use App\Model\TrackingsLog;
use App\Model\Customer;
use App\Model\PostCode;
use Validator;
use Hash;
use App\Http\Resources\UserResource;
use Auth;

class ApplicationController2 extends Controller
{
    
    public function courier_tracking_list(Request $request) {
        if ($request->id){
            $sql = "SELECT a.id, b.tracking_no, c.cust_name, c.cust_address, d.name_th , e.name_th as name_th1, f.name_th as name_th2, c.cust_postcode, c.cust_phone  FROM transfers a LEFT JOIN trackings b ON a.transfer_tracking_id = b.id LEFT JOIN customers c ON b.tracking_receiver_id = c.id LEFT JOIN districts d ON c.cust_sub_district = d.id LEFT JOIN amphures e ON c.cust_district = e.id LEFT JOIN provinces f ON c.cust_province = f.id WHERE a.transfer_courier_id = '$Request->courier_id' AND a.transfer_tracking_id = '$Request->tracking_id'  AND a.transfer_status = 'TransferToCourier'";
            $tracking_detail = DB::select($sql);

            return '{
                "tracking_id":"'.$tracking_detail[0]->id.'",
                "tracking_no":"'.$tracking_detail[0]->tracking_no.'",
                "customer_name":"'.$tracking_detail[0]->cust_name.'",
                "customer_phone":"'.$tracking_detail[0]->cust_phone.'",
                "customer_address":"'.$tracking_detail[0]->cust_address.' '."ต.".''.$tracking_detail[0]->name_th.' '."อ.".''.$tracking_detail[0]->name_th1.' '."อ.".''.$tracking_detail[0]->name_th2.'",
                "customer_zipcode":"'.$tracking_detail[0]->cust_postcode.'"
            }';
        }else{
            return '{"status":"0","msg":"กรุณากรอกข้อมูลให้ครบ"}';
        }
    }
}
