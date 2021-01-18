<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Admin;
use App\Model\User;
use App\Model\Employee;
use App\Model\Tracking;
use App\Model\SubTracking;
use App\Model\TrackingsLog;
use App\Model\Customer;
use App\Model\PostCode;
use App\Model\Transfer;
use App\Model\TranserBill;
use App\Model\CourierCall;
use App\Model\CourierLogin;
use App\Model\CourierRequestPassword;
use App\Model\Booking;
use App\Model\PacelCare;
use App\Model\TransferDropCenter;
use App\Model\TransferDropCenterBill;
use App\Model\RequestService;
use App\Model\CustomerCod;
use App\Model\province;
use App\Model\amphure;
use App\Model\District;
use App\Model\ParcelType;
use App\Model\ProductPrice;
use App\Model\ParcelPrice;
use App\Model\DimensionHistory;
use App\Model\SaleOther;
use Validator;
use Hash;
use App\Http\Resources\UserResource;
use Auth;
use DB;
class ApplicationReciveController extends Controller
{

    public function create_booking_recive(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
        
            if($request->booking_id != ""){
                $validator = Validator::make($request->all(), [
                    'booking_id' => 'required',
                    'courier_id' => 'required',
                    'customer_id' => 'required'
                ]);
            
                if($validator->fails()) {
                    return '{"status":"0","msg":"error 341"}';
                } 
                $booking = Booking::find($request->booking_id);
                if($booking){
                    $booking->update([
                        'booking_sender_id' => $request->customer_id
                    ]);

                    $RequestService = RequestService::where('request_booking_id', $booking->id)->where('request_currier_id', $request->courier_id)->first();
                    $RequestService->update([
                        'request_sender_id' => $request->customer_id
                    ]);

                    $CustomerCod = CustomerCod::where('customer_id', $booking->customer->id)->where('cod_status', '1')->first();
                    if($CustomerCod){
                        $cod_account = '1';
                    }else{
                        $cod_account = '0';
                    }
                    $Trackings = Tracking::where('tracking_booking_id', $booking->id)->where('tracking_no', 'not like', 'Destroy')->get();
                    // dd($Trackings);
                    $totle = 0;
                    $track_content = "[";
                    foreach ($Trackings as $key => $Tracking) {
                        $totle += $Tracking->tracking_amount;
                        $sql = "SELECT CONCAT('พัสดุ ',b.dimension_history_weigth, ' g ', b.dimension_history_width, 'x' ,b.dimension_history_length, 'x' ,b.dimension_history_hight) AS wlh FROM sub_trackings a LEFT JOIN dimension_histories b ON b.dimension_history_subtracking_id = a.id WHERE a.subtracking_tracking_id = '$Tracking->id' order by b.created_at asc";
                        $demention = DB::select($sql);
                        $dementions = json_encode($demention, JSON_UNESCAPED_UNICODE);
                        if($key == 0){
                            $track_content .= '{
                                "track_id":"'.$Tracking->id.'",
                                "cus_recive_name":"'.$Tracking->receiver->cust_name.'",
                                "service_fee":"'.$Tracking->tracking_amount.'",
                                "parcel_dimention":'.$dementions.'
                            }';
                        }else{
                            $track_content .= ',{
                                "track_id":"'.$Tracking->id.'",
                                "cus_recive_name":"'.$Tracking->receiver->cust_name.'",
                                "service_fee":"'.$Tracking->tracking_amount.'",
                                "parcel_dimention":'.$dementions.'
                            }';
                        }
                    }
                    $track_content .= "]";
                    $content = '{
                        "booking_id":"'.$booking->id.'",
                        "booking_no":"'.$booking->booking_no.'",
                        "cus_sender":"'.$booking->customer->cust_name.'",
                        "cus_sender_phone":"'.$booking->customer->cust_phone.'",
                        "cus_sender_zipcode":"'.$booking->customer->cust_postcode.'",
                        "cod_account":"'.$cod_account.'",
                        "totle_amount":"'.$totle.'",
                        "tracking":'.$track_content.'
                    }';

                    return $content;

                }else{
                    return '{"status":"0","msg":"error 342"}';
                }
                
            }else{
                $validator = Validator::make($request->all(), [
                    'courier_id' => 'required',
                    'customer_id' => 'required'
                ]);
            
                if($validator->fails()) {
                    return '{"status":"0","msg":"error 341"}';
                } 

                $employee = Employee::find($request->courier_id);
                // generate receive document no
                $countRow = Booking::where('booking_branch_id',$employee->emp_branch_id)->get();
                $num_row = count($countRow);
                $digit = count($countRow)+11;
                // dd($digit);

                $num_row < 99999 ? $documentNo = "SE00000000".$digit : null;
                $num_row < 9999 ? $documentNo = "SE000000000".$digit : null;
                $num_row < 999 ? $documentNo = "SE0000000000".$digit : null;
                $num_row < 99 ? $documentNo = "SE00000000000".$digit : null;
                $num_row < 9 ? $documentNo = "SE000000000000".$digit : null;
                $num_row == 0 ? $documentNo = "SE000000000000".$digit : null;

                $jobs_status = "request";

                $booking = Booking::create([
                    'booking_no' => $documentNo,
                    'booking_branch_id' => $employee->emp_branch_id,
                    'booking_sender_id' => $request->customer_id,
                    'booking_type' => 2,
                    'booking_status' => $jobs_status,  
                    'create_by' => $employee->id
                ]);
                
                $requestService = RequestService::create([
                    'request_booking_id'  => $booking->id, 
                    'request_sender_id'  => $booking->booking_sender_id, 
                    'request_currier_id'  => $request->courier_id,  
                    'request_status'  => "request",
                    'branch_id'  => $employee->emp_branch_id,
                    'request_parcel_qty'  => '1',  
                    'request_booking_no'  => $booking->booking_no,  
                ]);

                $CustomerCod = CustomerCod::where('customer_id', $booking->customer->id)->where('cod_status', '1')->first();
                if($CustomerCod){
                    $cod_account = '1';
                }else{
                    $cod_account = '0';
                }
                $Trackings = Tracking::where('tracking_booking_id', $booking->id)->where('tracking_no', 'not like', 'Destroy')->get();
                // dd($Trackings);
                $totle = 0;
                $track_content = "[";
                foreach ($Trackings as $key => $Tracking) {
                    $totle += $Tracking->tracking_amount;
                    $sql = "SELECT CONCAT('พัสดุ ',b.dimension_history_weigth, ' g ', b.dimension_history_width, 'x' ,b.dimension_history_length, 'x' ,b.dimension_history_hight) AS wlh FROM sub_trackings a LEFT JOIN dimension_histories b ON b.dimension_history_subtracking_id = a.id WHERE a.subtracking_tracking_id = '$Tracking->id' order by b.created_at asc";
                    $demention = DB::select($sql);
                    $dementions = json_encode($demention, JSON_UNESCAPED_UNICODE);
                    if($key == 0){
                        $track_content .= '{
                            "track_id":"'.$Tracking->id.'",
                            "cus_recive_name":"'.$Tracking->receiver->cust_name.'",
                            "service_fee":"'.$Tracking->tracking_amount.'",
                            "parcel_dimention":'.$dementions.'
                        }';
                    }else{
                        $track_content .= ',{
                            "track_id":"'.$Tracking->id.'",
                            "cus_recive_name":"'.$Tracking->receiver->cust_name.'",
                            "service_fee":"'.$Tracking->tracking_amount.'",
                            "parcel_dimention":'.$dementions.'
                        }';
                    }
                }
                $track_content .= "]";
                $content = '{
                    "booking_id":"'.$booking->id.'",
                    "booking_no":"'.$booking->booking_no.'",
                    "cus_sender":"'.$booking->customer->cust_name.'",
                    "cus_sender_phone":"'.$booking->customer->cust_phone.'",
                    "cus_sender_zipcode":"'.$booking->customer->cust_postcode.'",
                    "cod_account":"'.$cod_account.'",
                    "totle_amount":"'.$totle.'",
                    "tracking":'.$track_content.'
                }';

                return $content;
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function add_booking_new_customer(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'courier_id' => 'required',
                'cust_name' => 'required',
                'cust_address' => 'required',
                'cust_sub_district' => 'required',
                'cust_district' => 'required',
                'cust_province' => 'required',
                'cust_postcode' => 'required',
                'cust_phone' => 'required'    
            ]);
            if ($validator->fails()) {
                return '{"status":"0","msg":"error 221"}';
            }

            $customer = Customer::create([
                'cust_name' => $request->cust_name,
                'cust_address' => $request->cust_address,
                'cust_sub_district' => $request->cust_sub_district,
                'cust_district' => $request->cust_district,
                'cust_province' => $request->cust_province,
                'cust_postcode' => $request->cust_postcode,
                'cust_phone' => $request->cust_phone,
                'cust_status' => true
            ]);
            //customer_id

            $employee = Employee::find($request->courier_id);
            // generate receive document no
            $countRow = Booking::where('booking_branch_id',$employee->emp_branch_id)->get();
            $num_row = count($countRow);
            $digit = count($countRow)+11;
            // dd($digit);
            $request->customer_id = $customer->id;

            $num_row < 99999 ? $documentNo = "SE00000000".$digit : null;
            $num_row < 9999 ? $documentNo = "SE000000000".$digit : null;
            $num_row < 999 ? $documentNo = "SE0000000000".$digit : null;
            $num_row < 99 ? $documentNo = "SE00000000000".$digit : null;
            $num_row < 9 ? $documentNo = "SE000000000000".$digit : null;
            $num_row == 0 ? $documentNo = "SE000000000000".$digit : null;

            $jobs_status = "request";

            $booking = Booking::create([
                'booking_no' => $documentNo,
                'booking_branch_id' => $employee->emp_branch_id,
                'booking_sender_id' => $request->customer_id,
                'booking_type' => 2,
                'booking_status' => $jobs_status,  
                'create_by' => $employee->id
            ]);
            
            $requestService = RequestService::create([
                'request_booking_id'  => $booking->id, 
                'request_sender_id'  => $booking->booking_sender_id, 
                'request_currier_id'  => $request->courier_id,  
                'request_status'  => "request",
                'branch_id'  => $employee->emp_branch_id,
                'request_parcel_qty'  => '1',  
                'request_booking_no'  => $booking->booking_no,  
            ]);

            $CustomerCod = CustomerCod::where('customer_id', $booking->customer->id)->where('cod_status', '1')->first();
            if($CustomerCod){
                $cod_account = '1';
            }else{
                $cod_account = '0';
            }
            $Trackings = Tracking::where('tracking_booking_id', $booking->id)->where('tracking_no', 'not like', 'Destroy')->get();
            // dd($Trackings);
            $totle = 0;
            $track_content = "[";
            foreach ($Trackings as $key => $Tracking) {
                $totle += $Tracking->tracking_amount;
                $sql = "SELECT CONCAT('พัสดุ ',b.dimension_history_weigth, ' g ', b.dimension_history_width, 'x' ,b.dimension_history_length, 'x' ,b.dimension_history_hight) AS wlh FROM sub_trackings a LEFT JOIN dimension_histories b ON b.dimension_history_subtracking_id = a.id WHERE a.subtracking_tracking_id = '$Tracking->id' order by b.created_at asc";
                $demention = DB::select($sql);
                $dementions = json_encode($demention, JSON_UNESCAPED_UNICODE);
                if($key == 0){
                    $track_content .= '{
                        "track_id":"'.$Tracking->id.'",
                        "cus_recive_name":"'.$Tracking->receiver->cust_name.'",
                        "service_fee":"'.$Tracking->tracking_amount.'",
                        "parcel_dimention":'.$dementions.'
                    }';
                }else{
                    $track_content .= ',{
                        "track_id":"'.$Tracking->id.'",
                        "cus_recive_name":"'.$Tracking->receiver->cust_name.'",
                        "service_fee":"'.$Tracking->tracking_amount.'",
                        "parcel_dimention":'.$dementions.'
                    }';
                }
            }
            $track_content .= "]";
            $content = '{
                "booking_id":"'.$booking->id.'",
                "booking_no":"'.$booking->booking_no.'",
                "cus_sender":"'.$booking->customer->cust_name.'",
                "cus_sender_phone":"'.$booking->customer->cust_phone.'",
                "cus_sender_zipcode":"'.$booking->customer->cust_postcode.'",
                "cod_account":"'.$cod_account.'",
                "totle_amount":"'.$totle.'",
                "tracking":'.$track_content.'
            }';

            return $content;
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function search_sender(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->phone_num){
                $Customers = Customer::where('cust_phone', 'like', $request->phone_num.'%')->get();
                $cusdetail = '[';
                foreach ($Customers as $key => $Customer) {
                    $CustomerCod = CustomerCod::where('customer_id', $Customer->id)->where('cod_status', '1')->first();
                    if($CustomerCod){
                        $cod_account = '1';
                    }else{
                        $cod_account = '0';
                    }
                    if($key == 0){
                        $cusdetail .= '{
                            "cus_id":"'.$Customer->id.'",
                            "cus_name":"'.$Customer->cust_name.'",
                            "cus_Address":"'.$Customer->cust_address.' '.$Customer->District->name_th.' '.$Customer->amphure->name_th.' '.$Customer->province->name_th.'",
                            "cus_zipcode":"'.$Customer->cust_postcode.'",
                            "cust_phone":"'.$Customer->cust_phone.'",
                            "cus_zipcode":"'.$Customer->cust_postcode.'",
                            "cod_account":"'.$cod_account.'"
                        }';
                    }else{
                        $cusdetail .= ',{
                            "cus_id":"'.$Customer->id.'",
                            "cus_name":"'.$Customer->cust_name.'",
                            "cus_Address":"'.$Customer->cust_address.' '.$Customer->District->name_th.' '.$Customer->amphure->name_th.' '.$Customer->province->name_th.'",
                            "cus_zipcode":"'.$Customer->cust_postcode.'",
                            "cust_phone":"'.$Customer->cust_phone.'",
                            "cus_zipcode":"'.$Customer->cust_postcode.'",
                            "cod_account":"'.$cod_account.'"
                        }';
                    }
                }
                $cusdetail .= ']';
                return $cusdetail;
            }else{
                return '{"status":"0","msg":"error 211"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function search_recive(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->phone_num){
                $Customers = Customer::where('cust_phone', 'like', $request->phone_num.'%')->get();
                $cusdetail = '[';
                foreach ($Customers as $key => $Customer) {
                    if($key == 0){
                        $cusdetail .= '{
                            "cus_id":"'.$Customer->id.'",
                            "cus_name":"'.$Customer->cust_name.'",
                            "cus_Address":"'.$Customer->cust_address.' '.$Customer->District->name_th.' '.$Customer->amphure->name_th.' '.$Customer->province->name_th.'",
                            "cus_zipcode":"'.$Customer->cust_postcode.'",
                            "cust_phone":"'.$Customer->cust_phone.'",
                            "cus_zipcode":"'.$Customer->cust_postcode.'"
                        }';
                    }else{
                        $cusdetail .= ',{
                            "cus_id":"'.$Customer->id.'",
                            "cus_name":"'.$Customer->cust_name.'",
                            "cus_Address":"'.$Customer->cust_address.' '.$Customer->District->name_th.' '.$Customer->amphure->name_th.' '.$Customer->province->name_th.'",
                            "cus_zipcode":"'.$Customer->cust_postcode.'",
                            "cust_phone":"'.$Customer->cust_phone.'",
                            "cus_zipcode":"'.$Customer->cust_postcode.'"
                        }';
                    }
                }
                $cusdetail .= ']';
                return $cusdetail;
            }else{
                return '{"status":"0","msg":"error 241"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    public function update_cus_revice_tracking(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required',
                'cus_id' => 'required'   
            ]);
            if ($validator->fails()) {
                return '{"status":"0","msg":"error 251"}';
            }
            $Customers = Customer::find($request->cus_id);
            $PostCode = PostCode::where('postcode',$Customers->cust_postcode)->first();
            if(!empty($PostCode)){
                $jobs_status = "new";
                $trackings = Tracking::create([
                    'tracking_booking_id' => $request->booking_id,
                    'tracking_receiver_id' => $request->cus_id,
                    'tracking_parcel_type' => '0',
                    'tracking_status' => $jobs_status
                ]);

                $Booking = Booking::find($request->booking_id);
                    $detail = '{';
                        $detail .= '
                            "tracking_id":"'.$trackings->id.'",
                            "sender":{
                                "cust_name":"'.$Booking->customer->cust_name.'",
                                "customer_address":"'.$Booking->customer->cust_address.' '."ต.".''.$Booking->customer->District->name_th.' '."อ.".''.$Booking->customer->amphure->name_th.' '."อ.".''.$Booking->customer->province->name_th.'",
                                "customer_zipcode":"'.$Booking->customer->cust_postcode.'",
                                "customer_phone":"'.$Booking->customer->cust_phone.'"
                            },';
                $Tracking = Tracking::find($trackings->id);
                        $detail .= '
                            "recive":{
                                "cust_name":"'.$Tracking->receiver->cust_name.'",
                                "customer_address":"'.$Tracking->receiver->cust_address.' '."ต.".''.$Tracking->receiver->District->name_th.' '."อ.".''.$Tracking->receiver->amphure->name_th.' '."อ.".''.$Tracking->receiver->province->name_th.'",
                                "customer_zipcode":"'.$Tracking->receiver->cust_postcode.'",
                                "customer_phone":"'.$Tracking->receiver->cust_phone.'"
                            }';
                    $detail .= '}';
                return $detail;
            }else{
                return '{"status":"0","msg":"error 252"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function destroy_tracking(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'tracking_id' => 'required',
                'courier_id' => 'required'   
            ]);
            if ($validator->fails()) {
                return '{"status":"0","msg":"error 391"}';
            }

            $id = $request->tracking_id;
            $selected_data = Tracking::find($id);
            if($selected_data){
                if($selected_data->tracking_no !== ""){
                    $bookingData = Booking::where('id',$selected_data->tracking_booking_id)->first();
                    // $customer = Customer::where('id',$bookingData->booking_sender_id)->first();
                    $tracking = Tracking::find($id);
                    $subtrackings = SubTracking::where('subtracking_tracking_id',$tracking->id)->get();

                    $saleOtherList = SaleOther::where('sale_other_tr_id',$tracking->id)->get();
                    // return $saleOtherList;
                    if(count($saleOtherList)>0){
                        foreach($saleOtherList as $saleOther) {
                            $saleOther->delete();
                        }
                    }

                    if(count($subtrackings)>0){
                        foreach ($subtrackings as $subtracking) {
                            $dimensionHistory = DimensionHistory::where('dimension_history_subtracking_id',$subtracking->id)->first();
                            $dimensionHistory->delete();
                            $subtracking->delete();
                        }
                    }

                    // $tracking->delete();
                    $tracking->update([
                        'tracking_no' => $tracking->tracking_no.'Destroy',
                        'tracking_amount' => 0
                    ]);

                    $trackingList = Tracking::where('tracking_no', '!=', '')->where('tracking_no', 'NOT LIKE', '%Destroy')->where('tracking_booking_id',$bookingData->id)->get();
                    $total_bill = 0;
                    foreach($trackingList as $tracking) {
                        $total_bill += $tracking->tracking_amount;
                    }

                    $booking = Booking::find($bookingData->id);
                    $booking->update([
                        'booking_amount'=> $total_bill
                    ]);
                }else{
                    $selected_data->delete();
                }
                return '{"status":"1"}';
            }else{
                return '{"status":"0","msg":"error 392"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    // public function destroy_booking(Request $request){
        // if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
        //     $validator = Validator::make($request->all(), [
        //         'tracking_id' => 'required',
        //         'cus_id' => 'required'   
        //     ]);
        //     if ($validator->fails()) {
        //         return '{"status":"0","msg":"error 261"}';
        //     }
            // }else{
            //     return  '{
            //                 "error":"0"
            //             }';
            // }
    // }
    
    public function update_cus_revice_tracking_new(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'tracking_id' => 'required',
                'cus_id' => 'required'   
            ]);
            if ($validator->fails()) {
                return '{"status":"0","msg":"error 261"}';
            }

            $Customers = Customer::find($request->cus_id);
            $PostCode = PostCode::where('postcode',$Customers->cust_postcode)->first();
            if(!empty($PostCode)){
                $Tracking = Tracking::find($request->tracking_id);
                $jobs_status = "new";
                $Tracking->update([
                    'tracking_receiver_id' => $request->cus_id
                ]);

                $Booking = Booking::find($Tracking->tracking_booking_id);
                    $detail = '{';
                        $detail .= '
                            "tracking_id":"'.$Tracking->id.'",
                            "sender":{
                                "cust_name":"'.$Booking->customer->cust_name.'",
                                "customer_address":"'.$Booking->customer->cust_address.' '."ต.".''.$Booking->customer->District->name_th.' '."อ.".''.$Booking->customer->amphure->name_th.' '."อ.".''.$Booking->customer->province->name_th.'",
                                "customer_zipcode":"'.$Booking->customer->cust_postcode.'",
                                "customer_phone":"'.$Booking->customer->cust_phone.'"
                            },';
                        $detail .= '
                            "recive":{
                                "cust_name":"'.$Tracking->receiver->cust_name.'",
                                "customer_address":"'.$Tracking->receiver->cust_address.' '."ต.".''.$Tracking->receiver->District->name_th.' '."อ.".''.$Tracking->receiver->amphure->name_th.' '."อ.".''.$Tracking->receiver->province->name_th.'",
                                "customer_zipcode":"'.$Tracking->receiver->cust_postcode.'",
                                "customer_phone":"'.$Tracking->receiver->cust_phone.'"
                            }';
                    $detail .= '}';
                return $detail;
            }else{
                return '{"status":"0","msg":"error 262"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function courier_add_customer(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required',
                'cust_name' => 'required',
                'cust_address' => 'required',
                'cust_sub_district' => 'required',
                'cust_district' => 'required',
                'cust_province' => 'required',
                'cust_postcode' => 'required',
                'cust_phone' => 'required'    
            ]);
            if ($validator->fails()) {
                return '{"status":"0","msg":"error 221"}';
            }

            $PostCode = PostCode::where('postcode',$request->cust_postcode)->first();
            if(!empty($PostCode)){
                $customer = Customer::create([
                    'cust_name' => $request->cust_name,
                    'cust_address' => $request->cust_address,
                    'cust_sub_district' => $request->cust_sub_district,
                    'cust_district' => $request->cust_district,
                    'cust_province' => $request->cust_province,
                    'cust_postcode' => $request->cust_postcode,
                    'cust_phone' => $request->cust_phone,
                    'cust_status' => true
                ]);

                $jobs_status = "new";
                $trackings = Tracking::create([
                    'tracking_booking_id' => $request->booking_id,
                    'tracking_receiver_id' => $customer->id,
                    'tracking_parcel_type' => '0',
                    'tracking_status' => $jobs_status
                ]);

                $Booking = Booking::find($request->booking_id);
                    $detail = '{';
                        $detail .= '
                            "tracking_id":"'.$trackings->id.'",
                            "sender":{
                                "cust_name":"'.$Booking->customer->cust_name.'",
                                "customer_address":"'.$Booking->customer->cust_address.' '."ต.".''.$Booking->customer->District->name_th.' '."อ.".''.$Booking->customer->amphure->name_th.' '."อ.".''.$Booking->customer->province->name_th.'",
                                "customer_zipcode":"'.$Booking->customer->cust_postcode.'",
                                "customer_phone":"'.$Booking->customer->cust_phone.'"
                            },';
                $Tracking = Tracking::find($trackings->id);
                        $detail .= '
                            "recive":{
                                "cust_name":"'.$Tracking->receiver->cust_name.'",
                                "customer_address":"'.$Tracking->receiver->cust_address.' '."ต.".''.$Tracking->receiver->District->name_th.' '."อ.".''.$Tracking->receiver->amphure->name_th.' '."อ.".''.$Tracking->receiver->province->name_th.'",
                                "customer_zipcode":"'.$Tracking->receiver->cust_postcode.'",
                                "customer_phone":"'.$Tracking->receiver->cust_phone.'"
                            }';
                    $detail .= '}';
                return $detail;
            }else{
                return '{"status":"0","msg":"error 222"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function get_addres_by_zipcode(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'cust_postcode' => 'required'
            ]);
            if ($validator->fails()) {
                return '{"status":"0","msg":"error 231"}';
            }
            $sql = "SELECT a.id, a.name_th FROM districts a LEFT JOIN amphures b ON b.id = a.amphure_id LEFT JOIN provinces c ON c.id = b.province_id WHERE a.zip_code = '$request->cust_postcode' GROUP BY a.id ORDER BY a.name_th ASC";
            $districts = DB::select($sql);
            $districts = json_encode($districts, JSON_UNESCAPED_UNICODE);
            
            $sql = "SELECT b.id, b.name_th FROM districts a LEFT JOIN amphures b ON b.id = a.amphure_id LEFT JOIN provinces c ON c.id = b.province_id WHERE a.zip_code = '$request->cust_postcode' GROUP BY b.id ORDER BY b.name_th ASC";
            $amphures = DB::select($sql);
            $amphures = json_encode($amphures, JSON_UNESCAPED_UNICODE);
            
            $sql = "SELECT c.id, c.name_th FROM districts a LEFT JOIN amphures b ON b.id = a.amphure_id LEFT JOIN provinces c ON c.id = b.province_id WHERE a.zip_code = '$request->cust_postcode' GROUP BY c.id ORDER BY c.name_th ASC";
            $provinces = DB::select($sql);
            $provinces = json_encode($provinces, JSON_UNESCAPED_UNICODE);

            $address = '{
                "provinces":'.$provinces.',
                "amphures":'.$amphures.',
                "districts":'.$districts.'
            }';

            return $address;
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function create_booking_tracking(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {}
        
    }
    
    public function Request_recive_list(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->courier_id){
                $RequestServices = RequestService::where('request_currier_id', $request->courier_id)
                                                    ->whereDate('created_at', DB::raw('CURDATE()'))
                                                    ->where('request_status', '!=', 'request-done')
                                                    ->where('request_status', '!=', 'done')
                                                    ->get();
                $content = '[';
                foreach ($RequestServices as $key => $RequestServices) {
                    if($RequestServices->request_parcel_qty == '1'){
                        $request_box = "1 ชิ้น";
                    }else if($RequestServices->request_parcel_qty == '2'){
                        $request_box = "2 ชิ้น";
                    }else if($RequestServices->request_parcel_qty == '3'){
                        $request_box = "3 ชิ้น";
                    }else if($RequestServices->request_parcel_qty == '4'){
                        $request_box = "4 ชิ้น";
                    }else if($RequestServices->request_parcel_qty == '5'){
                        $request_box = "5-10 ชิ้น";
                    }else if($RequestServices->request_parcel_qty == '6'){
                        $request_box = "มากกว่า 10 ชิ้น";
                    }
                    $call_zero = 0;
                    $call_one = 0;
                    $call_two = 0;
                    $color_status = "gray";
                    $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE request_service_id = '$RequestServices->id' AND courier_id = '$RequestServices->request_currier_id' order by created_at asc";
                    $courier_call_lists = DB::select($sql);
                    $call_status = json_encode($courier_call_lists, JSON_UNESCAPED_UNICODE);
                    foreach ($courier_call_lists as $courier_call_list) {
                        if($courier_call_list->callstatus == 0){
                            $call_zero += 1;
                        }else if($courier_call_list->callstatus == 1){
                            $call_one += 1;
                        }else if($courier_call_list->callstatus == 2){
                            $call_two += 1;
                        }
                    }
                    if($call_zero >= 1){
                        $color_status = "blue";
                    }else if($call_one >= 1){
                        $color_status = "red";
                    }else if($call_two >= 3){
                        $color_status = "red";
                    }else if($call_two >= 1){
                        $color_status = "blue";
                    }else{
                        $color_status = "gray";
                    }
                    if($key == '0'){

                        $content .= '{
                            "booking_id":"'.$RequestServices->request_booking_id.'",
                            "booking_no":"'.$RequestServices->request_booking_no.'",
                            "cusname":"'.$RequestServices->sender->cust_name.'",
                            "address":"'.$RequestServices->sender->cust_address.' '.$RequestServices->sender->District->name_th.' '.$RequestServices->sender->amphure->name_th.' '.$RequestServices->sender->province->name_th.'",
                            "zipcode":"'.$RequestServices->sender->cust_postcode.'",
                            "phone":"'.$RequestServices->sender->cust_phone.'",
                            "request_box":"'.$request_box.'",
                            "status":"'.$RequestServices->request_status.'",
                            "color":"'.$color_status.'",
                            "call_detail":'.$call_status.'
                        }';

                    }else{

                        $content .= ',{
                            "booking_id":"'.$RequestServices->request_booking_id.'",
                            "booking_no":"'.$RequestServices->request_booking_no.'",
                            "cusname":"'.$RequestServices->sender->cust_name.'",
                            "address":"'.$RequestServices->sender->cust_address.' '.$RequestServices->sender->District->name_th.' '.$RequestServices->sender->amphure->name_th.' '.$RequestServices->sender->province->name_th.'",
                            "zipcode":"'.$RequestServices->sender->cust_postcode.'",
                            "phone":"'.$RequestServices->sender->cust_phone.'",
                            "request_box":"'.$request_box.'",
                            "status":"'.$RequestServices->request_status.'",
                            "color":"'.$color_status.'",
                            "call_detail":'.$call_status.'
                        }';

                    }
                }
                $content .= ']';
                return $content;
            }else{
                return '{"status":"0","msg":"error 171"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function Request_recive_list_success(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->courier_id){
                $RequestServices = RequestService::where('request_currier_id', $request->courier_id)
                                                    ->whereDate('created_at', DB::raw('CURDATE()'))
                                                    ->where('request_status', 'request-done')
                                                    ->orwhere('request_currier_id', $request->courier_id)
                                                    ->whereDate('created_at', DB::raw('CURDATE()'))
                                                    ->where('request_status', 'done')
                                                    ->get();
                $content = '[';
                foreach ($RequestServices as $key => $RequestServices) {
                    if($RequestServices->request_parcel_qty == '1'){
                        $request_box = "1 ชิ้น";
                    }else if($RequestServices->request_parcel_qty == '2'){
                        $request_box = "2 ชิ้น";
                    }else if($RequestServices->request_parcel_qty == '3'){
                        $request_box = "3 ชิ้น";
                    }else if($RequestServices->request_parcel_qty == '4'){
                        $request_box = "4 ชิ้น";
                    }else if($RequestServices->request_parcel_qty == '5'){
                        $request_box = "5-10 ชิ้น";
                    }else if($RequestServices->request_parcel_qty == '6'){
                        $request_box = "มากกว่า 10 ชิ้น";
                    }
                    $call_zero = 0;
                    $call_one = 0;
                    $call_two = 0;
                    $color_status = "gray";
                    $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE request_service_id = '$RequestServices->id' AND courier_id = '$RequestServices->request_currier_id' order by created_at asc";
                    $courier_call_lists = DB::select($sql);
                    $call_status = json_encode($courier_call_lists, JSON_UNESCAPED_UNICODE);
                    foreach ($courier_call_lists as $courier_call_list) {
                        if($courier_call_list->callstatus == 0){
                            $call_zero += 1;
                        }else if($courier_call_list->callstatus == 1){
                            $call_one += 1;
                        }else if($courier_call_list->callstatus == 2){
                            $call_two += 1;
                        }
                    }
                    if($call_zero >= 1){
                        $color_status = "blue";
                    }else if($call_one >= 1){
                        $color_status = "red";
                    }else if($call_two >= 3){
                        $color_status = "red";
                    }else if($call_two >= 1){
                        $color_status = "blue";
                    }else{
                        $color_status = "gray";
                    }
                    // $Tracking = Tracking::whrer('tracking_booking_id', '$RequestServices->request_booking_id')
                    $sql = "SELECT tracking_no FROM trackings WHERE tracking_booking_id = '$RequestServices->request_booking_id' AND tracking_status = 'request-done' OR tracking_booking_id = '$RequestServices->request_booking_id' AND tracking_status = 'done'";
                    $All_tracking = DB::select($sql);
                    $All_trackings = json_encode($All_tracking, JSON_UNESCAPED_UNICODE);
                    if($key == '0'){

                        $content .= '{
                            "booking_id":"'.$RequestServices->request_booking_id.'",
                            "booking_no":"'.$RequestServices->request_booking_no.'",
                            "cusname":"'.$RequestServices->sender->cust_name.'",
                            "address":"'.$RequestServices->sender->cust_address.' '.$RequestServices->sender->District->name_th.' '.$RequestServices->sender->amphure->name_th.' '.$RequestServices->sender->province->name_th.'",
                            "zipcode":"'.$RequestServices->sender->cust_postcode.'",
                            "phone":"'.$RequestServices->sender->cust_phone.'",
                            "request_box":"'.$request_box.'",
                            "status":"'.$RequestServices->request_status.'",
                            "color":"'.$color_status.'",
                            "call_detail":'.$call_status.',
                            "all_tracking":'.$All_trackings.'
                        }';

                    }else{

                        $content .= ',{
                            "booking_id":"'.$RequestServices->request_booking_id.'",
                            "booking_no":"'.$RequestServices->request_booking_no.'",
                            "cusname":"'.$RequestServices->sender->cust_name.'",
                            "address":"'.$RequestServices->sender->cust_address.' '.$RequestServices->sender->District->name_th.' '.$RequestServices->sender->amphure->name_th.' '.$RequestServices->sender->province->name_th.'",
                            "zipcode":"'.$RequestServices->sender->cust_postcode.'",
                            "phone":"'.$RequestServices->sender->cust_phone.'",
                            "request_box":"'.$request_box.'",
                            "status":"'.$RequestServices->request_status.'",
                            "color":"'.$color_status.'",
                            "call_detail":'.$call_status.',
                            "all_tracking":'.$All_trackings.'
                        }';

                    }
                }
                $content .= ']';
                return $content;
            }else{
                return '{"status":"0","msg":"error 401"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function call_detail(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->booking_id && $request->courier_id != ""){
                $Booking = Booking::find($request->booking_id);
                $RequestService = RequestService::where('request_booking_id', $request->booking_id)->orderby('id', 'desc')->first();
                // $CourierCalls = CourierCall::where('request_service_id', $RequestService->id)->where('courier_id', $RequestService->request_currier_id)->get();
                // foreach ($variable as $key => $value) {
                //     # code...
                // }
                $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE request_service_id = '$RequestService->id' AND courier_id = '$request->courier_id' order by created_at asc";
                $courier_call_lists = DB::select($sql);
                $call_status = json_encode($courier_call_lists, JSON_UNESCAPED_UNICODE);

                $call_zero = 0;
                $call_one = 0;
                $call_two = 0;
                foreach ($courier_call_lists as $courier_call_list) {
                    if($courier_call_list->callstatus == 0){
                        $call_zero += 1;
                    }else if($courier_call_list->callstatus == 1){
                        $call_one += 1;
                    }else if($courier_call_list->callstatus == 2){
                        $call_two += 1;
                    }
                }
                if($call_zero >= 1){
                    $color_status = "blue";
                    $status = "พร้อมเข้ารับพัสดุ";
                }else if($call_one >= 1){
                    $color_status = "red";
                    $status = "ยกเลิการเข้ารับ/เบอร์ผิด";
                }else if($call_two >= 3){
                    $color_status = "red";
                    $status = "ติดปัญหา";
                }else if($call_two >= 1){
                    $color_status = "blue";
                    $status = "รอเข้ารับ";
                }else{
                    $color_status = "gray";
                    $status = "รอเข้ารับ";
                }

                return '{
                    "booking_id":"'.$Booking->id.'",
                    "booking_no":"'.$Booking->booking_no.'",
                    "status":"'.$status.'",
                    "color_status":"'.$color_status.'",
                    "sender":{
                        "cust_name":"'.$Booking->customer->cust_name.'",
                        "customer_phone":"'.$Booking->customer->cust_phone.'",
                        "customer_address":"'.$Booking->customer->cust_address.' '."ต.".''.$Booking->customer->District->name_th.' '."อ.".''.$Booking->customer->amphure->name_th.' '."อ.".''.$Booking->customer->province->name_th.'",
                        "customer_zipcode":"'.$Booking->customer->cust_postcode.'"
                    },
                    "call_history":'.$call_status.',
                    "problem":"'.$RequestService->action_status.'"
                }';
            }else{
                return '{"status":"0","msg":"error 181"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function courier_call_status_recive(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->booking_id && $request->callstatus != "" && $request->courier_id != ""){
                $RequestService = RequestService::where('request_booking_id', $request->booking_id)->where('request_currier_id', $request->courier_id)->orderby('id', 'desc')->first();
                if($RequestService){
                    $time = date('Y-m-d H:i:s');
                    $CourierCall = CourierCall::create([
                        'request_service_id' => $RequestService->id,
                        'courier_id' => $request->courier_id,
                        'callstatus' => $request->callstatus,
                        'note' => $request->note, 
                        'oncall' => $request->oncall, 
                        'ontalk' => $request->ontalk, 
                        'callTime' => $time
                    ]);

                    $RequestServices = RequestService::where('request_currier_id', $request->courier_id)
                                                    ->whereDate('created_at', DB::raw('CURDATE()'))
                                                    ->orwhere('request_currier_id', $request->courier_id)
                                                    ->where('request_status', 'request')
                                                    ->get();
                    $content = '[';
                    foreach ($RequestServices as $key => $RequestServices) {
                        $call_zero = 0;
                        $call_one = 0;
                        $call_two = 0;
                        $color_status = "gray";
                        $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE request_service_id = '$RequestServices->id' AND courier_id = '$RequestServices->request_currier_id' order by created_at asc";
                        $courier_call_lists = DB::select($sql);
                        $call_status = json_encode($courier_call_lists, JSON_UNESCAPED_UNICODE);
                        foreach ($courier_call_lists as $courier_call_list) {
                            if($courier_call_list->callstatus == 0){
                                $call_zero += 1;
                            }else if($courier_call_list->callstatus == 1){
                                $call_one += 1;
                            }else if($courier_call_list->callstatus == 2){
                                $call_two += 1;
                            }
                        }
                        if($call_zero >= 1){
                            $color_status = "blue";
                        }else if($call_one >= 1){
                            $color_status = "red";
                        }else if($call_two >= 3){
                            $color_status = "red";
                        }else if($call_two >= 1){
                            $color_status = "blue";
                        }else{
                            $color_status = "gray";
                        }
                        if($key == '0'){

                            $content .= '{
                                "booking_id":"'.$RequestServices->request_booking_id.'",
                                "booking_no":"'.$RequestServices->request_booking_no.'",
                                "cusname":"'.$RequestServices->sender->cust_name.'",
                                "address":"'.$RequestServices->sender->cust_address.' '.$RequestServices->sender->District->name_th.' '.$RequestServices->sender->amphure->name_th.' '.$RequestServices->sender->province->name_th.'",
                                "zipcode":"'.$RequestServices->sender->cust_postcode.'",
                                "phone":"'.$RequestServices->sender->cust_phone.'",
                                "status":"'.$RequestServices->request_status.'",
                                "color":"'.$color_status.'",
                                "call_detail":'.$call_status.'
                            }';

                        }else{

                            $content .= ',{
                                "booking_id":"'.$RequestServices->request_booking_id.'",
                                "booking_no":"'.$RequestServices->request_booking_no.'",
                                "cusname":"'.$RequestServices->sender->cust_name.'",
                                "address":"'.$RequestServices->sender->cust_address.' '.$RequestServices->sender->District->name_th.' '.$RequestServices->sender->amphure->name_th.' '.$RequestServices->sender->province->name_th.'",
                                "zipcode":"'.$RequestServices->sender->cust_postcode.'",
                                "phone":"'.$RequestServices->sender->cust_phone.'",
                                "status":"'.$RequestServices->request_status.'",
                                "color":"'.$color_status.'",
                                "call_detail":'.$call_status.'
                            }';

                        }
                    }
                    $content .= ']';
                    return $content;
                }else{
                    return '{"status":"0","msg":"error 192"}';
                }
            }else{
                return '{"status":"0","msg":"error 191"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function Request_recive_booking(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->booking_id){
                $Booking = Booking::find($request->booking_id);
                $CustomerCod = CustomerCod::where('customer_id', $Booking->customer->id)->where('cod_status', '1')->first();
                if($CustomerCod){
                    $cod_account = '1';
                }else{
                    $cod_account = '0';
                }
                $Trackings = Tracking::where('tracking_booking_id', $Booking->id)->where('tracking_no', 'not like', 'Destroy')->get();
                // dd($Trackings);
                $totle = 0;
                $track_content = "[";
                foreach ($Trackings as $key => $Tracking) {
                    $totle += $Tracking->tracking_amount;
                    $sql = "SELECT CONCAT('พัสดุ ',b.dimension_history_weigth, ' g ', b.dimension_history_width, 'x' ,b.dimension_history_length, 'x' ,b.dimension_history_hight) AS wlh FROM sub_trackings a LEFT JOIN dimension_histories b ON b.dimension_history_subtracking_id = a.id WHERE a.subtracking_tracking_id = '$Tracking->id' order by b.created_at asc";
                    $demention = DB::select($sql);
                    $dementions = json_encode($demention, JSON_UNESCAPED_UNICODE);
                    if($key == 0){
                        $track_content .= '{
                            "track_id":"'.$Tracking->id.'",
                            "cus_recive_name":"'.$Tracking->receiver->cust_name.'",
                            "service_fee":"'.$Tracking->tracking_amount.'",
                            "parcel_dimention":'.$dementions.'
                        }';
                    }else{
                        $track_content .= ',{
                            "track_id":"'.$Tracking->id.'",
                            "cus_recive_name":"'.$Tracking->receiver->cust_name.'",
                            "service_fee":"'.$Tracking->tracking_amount.'",
                            "parcel_dimention":'.$dementions.'
                        }';
                    }
                }
                $track_content .= "]";
                $content = '{
                    "booking_id":"'.$Booking->id.'",
                    "booking_no":"'.$Booking->booking_no.'",
                    "cus_sender":"'.$Booking->customer->cust_name.'",
                    "cus_sender_phone":"'.$Booking->customer->cust_phone.'",
                    "cus_sender_zipcode":"'.$Booking->customer->cust_postcode.'",
                    "cod_account":"'.$cod_account.'",
                    "totle_amount":"'.$totle.'",
                    "tracking":'.$track_content.'
                }';

                return $content;
            }else{
                return '{"status":"0","msg":"error 201"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function recive_add_parcel(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'parcelType_id' => 'required',
                'selected_dimension_type' => 'required',
                'tracking_id' => 'required',
                'weigth' => 'required'
            ]);
            if($request->subtracking_cod == null){
                $request->subtracking_cod = 0;
            }

            if ($validator->fails()) {
                return '{"status":"0","msg":"error 271"}';
            }

            $parcelPrices = ParcelPrice::where('parcel_total_dimension','!=','COD')->get();
            // dd($parcelPrices);
            $datas = [];
            foreach ($parcelPrices as $parcelPrice) {
                $maxweigth[] = $parcelPrice->parcel_total_weight;
                $maxdimension[] = $parcelPrice->parcel_total_dimension;
            }
            $maxweigth = max($maxweigth);
            $maxdimension = max($maxdimension);
            // dd($maxprice, $dimension);
            // dd($request->subtracking_tracking_id);
            if($request->selected_dimension_type=='2'){
                $width = $request->width;
                $hight = $request->hight;
                $length = $request->length;
                $product_dimension = $width+$hight+$length;

                if($product_dimension > $maxdimension || $request->weigth > $maxweigth){
                    if($product_dimension > $maxdimension){
                        return '{"status":"0","msg":"error 272"}';
                    }else if($request->weigth > $maxweigth){
                        return '{"status":"0","msg":"error 273"}';
                    }

                    // $trackings = Tracking::where('id',$request->tracking_id)->first();
                    // $booking = Booking::find($trackings->tracking_booking_id);
                    // $Customer_sender = Customer::find($booking->booking_sender_id);
                    // $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
                    // $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
                    // $parcelTypes= ParcelType::get();
                    // $productPrices=ProductPrice::get();
                    // $user = Auth::user();
                    // $employee = Employee::where('id',$user->employee_id)->first();
                    // $saleOtherList = SaleOther::where('sale_other_tr_id',$trackings->id)->get();
                    // return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','saleOtherList','Customer_sender']));
                }else{
                    // ต้องวิ่งเอาdimensionไปเก็บด้วย
                    $subTracking = DimensionHistory::create([
                        'dimension_history_tracking_id' => $request->tracking_id,
                        'dimension_history_width' => $width,
                        'dimension_history_hight' => $hight,
                        'dimension_history_length' => $length,
                        'dimension_history_total_dimension' => $product_dimension,
                        'dimension_history_weigth' => $request->weigth,
                        'dimension_history_status' => 'done',
                    ]);
                }

            }else{
                $productPriceRank = ProductPrice::find($request->selected_dimension_value);
                $product_dimension = $productPriceRank->product_dimension;

                if($product_dimension > $maxdimension || $request->weigth > $maxweigth){
                    if($product_dimension > $maxdimension){
                        return '{"status":"0","msg":"error 272"}';
                        
                    }else if($request->weigth > $maxweigth){
                        return '{"status":"0","msg":"error 273"}';
                    }
                    // $trackings = Tracking::where('id',$request->tracking_id)->first();
                    // $booking = Booking::find($trackings->tracking_booking_id);
                    // $Customer_sender = Customer::find($booking->booking_sender_id);
                    // $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
                    // $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
                    // $parcelTypes= ParcelType::get();
                    // $productPrices=ProductPrice::get();
                    // $user = Auth::user();
                    // $employee = Employee::where('id',$user->employee_id)->first();
                    // $saleOtherList = SaleOther::where('sale_other_tr_id',$trackings->id)->get();
                    // return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','saleOtherList','Customer_sender']));
                }else{
                    // ต้องวิ่งเอาdimensionไปเก็บด้วย
                    $dimensionHistory = DimensionHistory::create([
                        'dimension_history_tracking_id' => $request->tracking_id,
                        'dimension_history_width' => $productPriceRank->product_width,
                        'dimension_history_hight' => $productPriceRank->product_hight,
                        'dimension_history_length' => $productPriceRank->product_length,
                        'dimension_history_total_dimension' => $productPriceRank->product_dimension,
                        'dimension_history_weigth' => $request->weigth,
                        'dimension_history_status' => 'done',
                    ]);   
                }

            }
            // dd($product_dimension);
            // คำนวนราคา กรณีเลือกdimension 
            $parcelPrices = ParcelPrice::where('parcel_total_dimension','!=','COD')->get();
            $datas = [];
            foreach ($parcelPrices as $parcelPrice) {
                if($parcelPrice->parcel_total_dimension >= $product_dimension) {
                    $rankPrice = $parcelPrice->parcel_price;
                    $datas[] = $rankPrice;
                }
            }
            $dimension_price = min($datas);  //ราคาจาก weight

            $weigth = $request->weigth;

            $wdatas = [];
            foreach ($parcelPrices as $parcelPrice) {
                if($parcelPrice->parcel_total_weight >= $weigth) {
                    $weigth_rankPrice = $parcelPrice->parcel_price;
                    $wdatas[] = $weigth_rankPrice;
                }
            }
            $weigth_price = min($wdatas); //ราคาจากน้ำหนัก

            $dimension_price > $weigth_price ? $count_parcel_price = $dimension_price : $count_parcel_price = $weigth_price;
            $tracking = Tracking::where('id',$request->tracking_id)->first();
            $booking_id = $tracking->tracking_booking_id;
            $subTracking = SubTracking::get();
            $countRow = count($subTracking)+1;
            $subtracking_no = date("Ymd").$countRow;
            $CODprice = ParcelPrice::where('parcel_total_dimension','=','COD')->first();
            $subtracking_cod_fee = $request->subtracking_cod*($CODprice->parcel_price/100);
            $subTracking = SubTracking::create([
                'subtracking_no' => $subtracking_no,
                'subtracking_booking_id' => $booking_id,
                'subtracking_tracking_id' => $request->tracking_id,
                'subtracking_dimension_type' => $request->selected_dimension_type,
                'subtracking_cod' => $request->subtracking_cod,
                'subtracking_cod_fee' => $subtracking_cod_fee,
                'subtracking_price' => $count_parcel_price,
                'subtracking_status' => "new",
                'subtracking_parcel_type' => $request->parcelType_id
            ]);

            $updateSubtrackingIdToHistoryDimension = DimensionHistory::where('dimension_history_tracking_id',$request->tracking_id)
            ->where('dimension_history_subtracking_id', null)
            ->first();

            $updateSubtrackingIdToHistoryDimension->update([
                'dimension_history_subtracking_id' => $subTracking->id
            ]);

            $subTrackings = SubTracking::where('subtracking_tracking_id',$request->tracking_id)->get();
            $amount = 0;
            foreach($subTrackings as $subTracking){
                $amount += $subTracking->subtracking_price;
                $amount += $subTracking->subtracking_cod_fee;
            }

            $SaleOthers = SaleOther::where('sale_other_tr_id',$request->tracking_id)->get();
            foreach($SaleOthers as $SaleOtherabject){
                $amount += $SaleOtherabject->sale_other_price;
            }

            $trackings = Tracking::where('id',$request->tracking_id)->first();

            if($trackings->tracking_no == ''){
                    $date = date('Y-m-d');
                    $track_row = Tracking::where('tracking_no', '!=', '')->whereDate('created_at',$date)->get();
                    $num_row = count($track_row);
                    $digit = $num_row+1;

                    $num_row < 999999 ? $documentNo = "".$digit : null;
                    $num_row < 99999 ? $documentNo = "0".$digit : null;
                    $num_row < 9999 ? $documentNo = "00".$digit : null;
                    $num_row < 999 ? $documentNo = "000".$digit : null;
                    $num_row < 99 ? $documentNo = "0000".$digit : null;
                    $num_row < 9 ? $documentNo = "00000".$digit : null;
                    $num_row == 0 ? $documentNo = "00000".$digit : null;

                    $th_year = date('y')+43;
                    $tracking_no = "SEV".date("dm").$th_year.$documentNo;

                $trackings->update([
                    'tracking_no' => $tracking_no,
                    'tracking_amount' => $amount
                ]);
            }else{
                $trackings->update([
                    'tracking_amount' => $amount
                ]);
            }
            $Booking = Booking::find($tracking->tracking_booking_id);
                $detail = '{';
                    $detail .= '
                        "tracking_id":"'.$trackings->id.'",
                        "sender":{
                            "cust_name":"'.$Booking->customer->cust_name.'",
                            "customer_address":"'.$Booking->customer->cust_address.' '."ต.".''.$Booking->customer->District->name_th.' '."อ.".''.$Booking->customer->amphure->name_th.' '."อ.".''.$Booking->customer->province->name_th.'",
                            "customer_zipcode":"'.$Booking->customer->cust_postcode.'",
                            "customer_phone":"'.$Booking->customer->cust_phone.'"
                        },';
            $Tracking = Tracking::find($trackings->id);
                    $detail .= '
                        "recive":{
                            "cust_name":"'.$Tracking->receiver->cust_name.'",
                            "customer_address":"'.$Tracking->receiver->cust_address.' '."ต.".''.$Tracking->receiver->District->name_th.' '."อ.".''.$Tracking->receiver->amphure->name_th.' '."อ.".''.$Tracking->receiver->province->name_th.'",
                            "customer_zipcode":"'.$Tracking->receiver->cust_postcode.'",
                            "customer_phone":"'.$Tracking->receiver->cust_phone.'"
                        },';
            $sql = "SELECT a.id, CONCAT('พัสดุ ',b.dimension_history_weigth, ' g ', b.dimension_history_width, 'x' ,b.dimension_history_length, 'x' ,b.dimension_history_hight) AS wlh, (a.subtracking_cod_fee+a.subtracking_price) AS totle FROM sub_trackings a LEFT JOIN dimension_histories b ON b.dimension_history_subtracking_id = a.id WHERE a.subtracking_tracking_id = '$trackings->id'";
            $subtrack = DB::select($sql);
            $subtracks = json_encode($subtrack, JSON_UNESCAPED_UNICODE);
                    $detail .= '
                        "subtrack":'.$subtracks.'
                        ';

                $detail .= '}';
            return $detail;
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function parcel_option(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $sql = "SELECT a.id, a.parcel_type_name FROM parcel_types a ORDER BY a.parcel_type_name ASC";
            $parcelTypes = DB::select($sql);
            $parcelTypes = json_encode($parcelTypes, JSON_UNESCAPED_UNICODE);

            $sql = "SELECT a.id, a.product_name, a.product_width, a.product_length, a.product_hight FROM product_prices a ORDER BY a.id ASC";
            $productPrices = DB::select($sql);
            $productPrices = json_encode($productPrices, JSON_UNESCAPED_UNICODE);

            $option = '{
                "parcelTypes":'.$parcelTypes.',
                "productPrices":'.$productPrices.'
            }';

            return $option;
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function destroy_subtracking(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'subtrack_id' => 'required'
            ]);

            if ($validator->fails()) {
                return '{"status":"0","msg":"error 281"}';
            }
            $selected_data = SubTracking::find($request->subtrack_id);
            $trackings = Tracking::where('id',$selected_data->subtracking_tracking_id)->first();
            $booking = Booking::find($trackings->tracking_booking_id);
            $Customer_sender = Customer::find($booking->booking_sender_id);
            $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
            $subTracking = SubTracking::find($request->subtrack_id);

            $dimensionHistory = DimensionHistory::where('dimension_history_subtracking_id',$subTracking->id)->first();
            $dimensionHistory->delete();

            $subTracking->delete();
            
            $subTrackings = SubTracking::where('subtracking_tracking_id',$selected_data->subtracking_tracking_id)->get();
            $amount = 0;
            foreach($subTrackings as $subTracking){
                $amount += $subTracking->subtracking_price;
                $amount += $subTracking->subtracking_cod_fee;
            }

            $SaleOthers = SaleOther::where('sale_other_tr_id',$selected_data->subtracking_tracking_id)->get();
            foreach($SaleOthers as $SaleOtherabject){
                $amount += $SaleOtherabject->sale_other_price;
            }
            // return $amount;

            $trackings->update([
                'tracking_amount' => $amount
            ]);

            $Booking = Booking::find($trackings->tracking_booking_id);
                $detail = '{';
                    $detail .= '
                        "tracking_id":"'.$trackings->id.'",
                        "sender":{
                            "cust_name":"'.$Booking->customer->cust_name.'",
                            "customer_address":"'.$Booking->customer->cust_address.' '."ต.".''.$Booking->customer->District->name_th.' '."อ.".''.$Booking->customer->amphure->name_th.' '."อ.".''.$Booking->customer->province->name_th.'",
                            "customer_zipcode":"'.$Booking->customer->cust_postcode.'",
                            "customer_phone":"'.$Booking->customer->cust_phone.'"
                        },';
            $Tracking = Tracking::find($trackings->id);
                    $detail .= '
                        "recive":{
                            "cust_name":"'.$Tracking->receiver->cust_name.'",
                            "customer_address":"'.$Tracking->receiver->cust_address.' '."ต.".''.$Tracking->receiver->District->name_th.' '."อ.".''.$Tracking->receiver->amphure->name_th.' '."อ.".''.$Tracking->receiver->province->name_th.'",
                            "customer_zipcode":"'.$Tracking->receiver->cust_postcode.'",
                            "customer_phone":"'.$Tracking->receiver->cust_phone.'"
                        },';
            $sql = "SELECT a.id, CONCAT('พัสดุ ',b.dimension_history_weigth, ' g ', b.dimension_history_width, 'x' ,b.dimension_history_length, 'x' ,b.dimension_history_hight) AS wlh, (a.subtracking_cod_fee+a.subtracking_price) AS totle FROM sub_trackings a LEFT JOIN dimension_histories b ON b.dimension_history_subtracking_id = a.id WHERE a.subtracking_tracking_id = '$trackings->id'";
            $subtrack = DB::select($sql);
            $subtracks = json_encode($subtrack, JSON_UNESCAPED_UNICODE);
                    $detail .= '
                        "subtrack":'.$subtracks.'
                        ';

                $detail .= '}';
            return $detail;
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function connect_tracking(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'track_id' => 'required'
            ]);

            if ($validator->fails()) {
                return '{"status":"0","msg":"error 291"}';
            }
            $Tracking = Tracking::find($request->track_id);
            $Booking = Booking::find($Tracking->tracking_booking_id);
                $detail = '{';
                    $detail .= '
                        "tracking_id":"'.$Tracking->id.'",
                        "sender":{
                            "cust_name":"'.$Booking->customer->cust_name.'",
                            "customer_address":"'.$Booking->customer->cust_address.' '."ต.".''.$Booking->customer->District->name_th.' '."อ.".''.$Booking->customer->amphure->name_th.' '."อ.".''.$Booking->customer->province->name_th.'",
                            "customer_zipcode":"'.$Booking->customer->cust_postcode.'",
                            "customer_phone":"'.$Booking->customer->cust_phone.'"
                        },';
                    $detail .= '
                        "recive":{
                            "cust_name":"'.$Tracking->receiver->cust_name.'",
                            "customer_address":"'.$Tracking->receiver->cust_address.' '."ต.".''.$Tracking->receiver->District->name_th.' '."อ.".''.$Tracking->receiver->amphure->name_th.' '."อ.".''.$Tracking->receiver->province->name_th.'",
                            "customer_zipcode":"'.$Tracking->receiver->cust_postcode.'",
                            "customer_phone":"'.$Tracking->receiver->cust_phone.'"
                        },';
            $sql = "SELECT a.id, CONCAT('พัสดุ ',b.dimension_history_weigth, ' g ', b.dimension_history_width, 'x' ,b.dimension_history_length, 'x' ,b.dimension_history_hight) AS wlh, (a.subtracking_cod_fee+a.subtracking_price) AS totle FROM sub_trackings a LEFT JOIN dimension_histories b ON b.dimension_history_subtracking_id = a.id WHERE a.subtracking_tracking_id = '$Tracking->id'";
            $subtrack = DB::select($sql);
            $subtracks = json_encode($subtrack, JSON_UNESCAPED_UNICODE);
                    $detail .= '
                        "subtrack":'.$subtracks.'
                        ';

                $detail .= '}';
            return $detail;
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function save_tracking(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'track_id' => 'required'
            ]);

            if ($validator->fails()) {
                return '{"status":"0","msg":"error 301"}';
            }
            $countRow_subTracking = SubTracking::where('subtracking_tracking_id',$request->track_id)->get();
            $countRow_saleOther = SaleOther::where('sale_other_tr_id',$request->track_id)->get();
            
            $countRow = count($countRow_saleOther) + count($countRow_subTracking);
            if($countRow > 0){
                $subtrackings = SubTracking::where('subtracking_tracking_id',$request->track_id)->get();
                $amount = 0;
                foreach ($subtrackings as $subtracking) {
                    $amount += $subtracking->subtracking_price;
                    $amount += $subtracking->subtracking_cod_fee;
                }

                $saleOtherList = SaleOther::where('sale_other_tr_id',$request->track_id)->get();
                $saleOtherAmount = 0;
                foreach($saleOtherList as $saleOther) {
                    $saleOtherAmount += $saleOther->sale_other_price;
                }
                $amount_Total = $amount + $saleOtherAmount;
                $tracking = Tracking::find($request->track_id);
                if($tracking){
                    $tracking->update([
                        'tracking_amount' => $amount_Total
                    ]);
                    $bookingAmount = 0;
                    $countBookingAmounts = Tracking::where('tracking_booking_id',$tracking->tracking_booking_id)->get();
                    foreach ($countBookingAmounts as $countBookingAmount) {
                        $bookingAmount += $countBookingAmount->tracking_amount;
                    }
                    $booking = Booking::find($tracking->tracking_booking_id);
                    if($booking){
                        $booking->update([
                            'booking_amount' => $bookingAmount
                        ]);
                    }

                    $Booking = Booking::find($tracking->tracking_booking_id);
                    $CustomerCod = CustomerCod::where('customer_id', $Booking->customer->id)->where('cod_status', '1')->first();
                    if($CustomerCod){
                        $cod_account = '1';
                    }else{
                        $cod_account = '0';
                    }
                    $Trackings = Tracking::where('tracking_booking_id', $Booking->id)->where('tracking_no', 'not like', 'Destroy')->get();
                    // dd($Trackings);
                    $totle = 0;
                    $track_content = "[";
                    foreach ($Trackings as $key => $Tracking) {
                        $totle += $Tracking->tracking_amount;
                        $sql = "SELECT CONCAT('พัสดุ ',b.dimension_history_weigth, ' g ', b.dimension_history_width, 'x' ,b.dimension_history_length, 'x' ,b.dimension_history_hight) AS wlh FROM sub_trackings a LEFT JOIN dimension_histories b ON b.dimension_history_subtracking_id = a.id WHERE a.subtracking_tracking_id = '$Tracking->id' order by b.created_at asc";
                        $demention = DB::select($sql);
                        $dementions = json_encode($demention, JSON_UNESCAPED_UNICODE);
                        if($key == 0){
                            $track_content .= '{
                                "track_id":"'.$Tracking->id.'",
                                "cus_recive_name":"'.$Tracking->receiver->cust_name.'",
                                "service_fee":"'.$Tracking->tracking_amount.'",
                                "parcel_dimention":'.$dementions.'
                            }';
                        }else{
                            $track_content .= ',{
                                "track_id":"'.$Tracking->id.'",
                                "cus_recive_name":"'.$Tracking->receiver->cust_name.'",
                                "service_fee":"'.$Tracking->tracking_amount.'",
                                "parcel_dimention":'.$dementions.'
                            }';
                        }
                    }
                    $track_content .= "]";
                    $content = '{
                        "booking_id":"'.$Booking->id.'",
                        "booking_no":"'.$Booking->booking_no.'",
                        "cus_sender":"'.$Booking->customer->cust_name.'",
                        "cus_sender_phone":"'.$Booking->customer->cust_phone.'",
                        "cus_sender_zipcode":"'.$Booking->customer->cust_postcode.'",
                        "cod_account":"'.$cod_account.'",
                        "totle_amount":"'.number_format($totle,2).'",
                        "tracking":'.$track_content.'
                    }';

                    return $content;
                }

            }else{
                return '{"status":"0","msg":"error 302"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function recive_save_booking(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required',
                'receive_money' => 'required',
                'courier_id' => 'required'
            ]);

            if ($validator->fails()) {
                return '{"status":"0","msg":"error 311"}';
            }
            $employee = Employee::find($request->courier_id);
            $checkRow = Tracking::where('tracking_booking_id',$request->booking_id)->get();
            if(count($checkRow) > 0){
                $checkRowSuccess = Tracking::where('tracking_booking_id',$request->booking_id)
                ->where('tracking_amount', null)
                ->where('tracking_no', 'not like', "%Destroy%")
                ->where('tracking_no', 'not like', "")
                ->get();
                if(count($checkRowSuccess)>0){
                    return '{"status":"0","msg":"error 314"}';
                }else{

                    $bookingData = Booking::find($request->booking_id);
                    if($request->receive_money >= $bookingData->booking_amount){
                        $booking_id = $bookingData->id;
                        $bookingData->update([
                            'booking_status' => "request-done",
                            'receive_money' => $request->receive_money
                        ]);

                        $trackings = Tracking::where('tracking_booking_id',$bookingData->id)->where('tracking_no', 'not like', "%Destroy%")->where('tracking_no', 'not like', "")->get();
                        $date = date('Y-m-d H:i:s');
                        $RequestServices = RequestService::where('request_booking_id', $request->booking_id)->where('request_currier_id', $request->courier_id)->first();
                        $RequestServices->update([
                            'request_status' => "request-done"
                        ]);
                        foreach ($trackings as $tracking) {
                            $PostCode = PostCode::where('postcode',$tracking->receiver->cust_postcode)->first();
                            if($PostCode->drop_center_id == $employee->emp_branch_id){
                                $tracking->update([
                                    'tracking_status' => "request-done",
                                    'orther_dc_revice_time' => $date
                                ]);
                            }else{
                                $tracking->update([
                                    'tracking_status' => "request-done"
                                ]);
                            }
                            $subTrackings = SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                            $i = 0;
                            foreach ($subTrackings as $subTracking) {
                                $i++;
                                $subTracking->update([
                                    'subtracking_under_tracking_id' => $i
                                ]);
                            }
                            $TrackingsLogs = TrackingsLog::create([
                                'tracking_no' => $tracking->tracking_no, 
                                'tracking_receiver_id' => $tracking->tracking_receiver_id,
                                'tracking_status_id' => 1, 
                                'tracking_branch_id_dc' => $bookingData->booking_branch_id, 
                                'tracking_branch_id_sub_dc' => 0,
                                'tracking_date' => $date
                            ]);
                            
                            $PacelCare = PacelCare::create([
                                'tracking_id' => $tracking->id, 
                                'doing_by' => $employee->id,
                                'branch_id' => $bookingData->booking_branch_id, 
                                'status' => 0, 
                                'ref_no' => $RequestServices->id
                            ]);
                        }
                        $trackingsEmptys = Tracking::where('tracking_booking_id',$bookingData->id)->where('tracking_no', "")->get();
                        foreach ($trackingsEmptys as $trackingsEmpty) {
                                $trackingsEmpty->delete();
                        }

                        $subTrackings = SubTracking::where('subtracking_booking_id',$bookingData->id)->get();
                        foreach ($subTrackings as $subTracking) {
                            $subTracking->update([
                                'subtracking_status' => "done"
                            ]);
                        }
                        $RequestServices = RequestService::where('request_booking_id', $request->booking_id)->where('request_currier_id', $request->courier_id)->first();
                        $RequestServices->update([
                            'request_status' => "request-done"
                        ]);

                        $RequestServices = RequestService::where('request_currier_id', $request->courier_id)
                                                        ->whereDate('created_at', DB::raw('CURDATE()'))
                                                        ->orwhere('request_currier_id', $request->courier_id)
                                                        ->where('request_status', 'request')
                                                        ->get();
                        $content = '[';
                        foreach ($RequestServices as $key => $RequestServices) {
                            if($RequestServices->request_parcel_qty == '1'){
                                $request_box = "1 ชิ้น";
                            }else if($RequestServices->request_parcel_qty == '2'){
                                $request_box = "2 ชิ้น";
                            }else if($RequestServices->request_parcel_qty == '3'){
                                $request_box = "3 ชิ้น";
                            }else if($RequestServices->request_parcel_qty == '4'){
                                $request_box = "4 ชิ้น";
                            }else if($RequestServices->request_parcel_qty == '5'){
                                $request_box = "5-10 ชิ้น";
                            }else if($RequestServices->request_parcel_qty == '6'){
                                $request_box = "มากกว่า 10 ชิ้น";
                            }
                            $call_zero = 0;
                            $call_one = 0;
                            $call_two = 0;
                            $color_status = "gray";
                            $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE request_service_id = '$RequestServices->id' AND courier_id = '$RequestServices->request_currier_id' order by created_at asc";
                            $courier_call_lists = DB::select($sql);
                            $call_status = json_encode($courier_call_lists, JSON_UNESCAPED_UNICODE);
                            foreach ($courier_call_lists as $courier_call_list) {
                                if($courier_call_list->callstatus == 0){
                                    $call_zero += 1;
                                }else if($courier_call_list->callstatus == 1){
                                    $call_one += 1;
                                }else if($courier_call_list->callstatus == 2){
                                    $call_two += 1;
                                }
                            }
                            if($call_zero >= 1){
                                $color_status = "blue";
                            }else if($call_one >= 1){
                                $color_status = "red";
                            }else if($call_two >= 3){
                                $color_status = "red";
                            }else if($call_two >= 1){
                                $color_status = "blue";
                            }else{
                                $color_status = "gray";
                            }
                            if($key == '0'){

                                $content .= '{
                                    "booking_id":"'.$RequestServices->request_booking_id.'",
                                    "booking_no":"'.$RequestServices->request_booking_no.'",
                                    "cusname":"'.$RequestServices->sender->cust_name.'",
                                    "address":"'.$RequestServices->sender->cust_address.' '.$RequestServices->sender->District->name_th.' '.$RequestServices->sender->amphure->name_th.' '.$RequestServices->sender->province->name_th.'",
                                    "zipcode":"'.$RequestServices->sender->cust_postcode.'",
                                    "phone":"'.$RequestServices->sender->cust_phone.'",
                                    "request_box":"'.$request_box.'",
                                    "status":"'.$RequestServices->request_status.'",
                                    "color":"'.$color_status.'",
                                    "call_detail":'.$call_status.'
                                }';

                            }else{

                                $content .= ',{
                                    "booking_id":"'.$RequestServices->request_booking_id.'",
                                    "booking_no":"'.$RequestServices->request_booking_no.'",
                                    "cusname":"'.$RequestServices->sender->cust_name.'",
                                    "address":"'.$RequestServices->sender->cust_address.' '.$RequestServices->sender->District->name_th.' '.$RequestServices->sender->amphure->name_th.' '.$RequestServices->sender->province->name_th.'",
                                    "zipcode":"'.$RequestServices->sender->cust_postcode.'",
                                    "phone":"'.$RequestServices->sender->cust_phone.'",
                                    "request_box":"'.$request_box.'",
                                    "status":"'.$RequestServices->request_status.'",
                                    "color":"'.$color_status.'",
                                    "call_detail":'.$call_status.'
                                }';

                            }
                        }
                        $content .= ']';
                        return $content;
                    }else{
                        return '{"status":"0","msg":"error 313"}';
                    }
                }
            }else{
                return '{"status":"0","msg":"error 312"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function Stuck_in_trouble(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required',
                'courier_id' => 'required',
                'problem' => 'required'
            ]);
            // dd($request->all());

            if ($validator->fails()) {
                return '{"status":"0","msg":"error 321"}';
            }
            $RequestService = RequestService::where('request_booking_id', $request->booking_id)->where('request_currier_id', $request->courier_id)->orderby('id', 'desc')->first();
            if($RequestService){
                $RequestService->update([
                    'request_status' => "stuck",
                    'action_status' => $request->problem
                ]);
                
                $Booking = Booking::find($request->booking_id);
                $Booking->update([
                    'booking_status' => "fail"
                ]);
                // $RequestService = RequestService::where('request_booking_id', $request->booking_id)->orderby('id', 'desc')->first();
                // $CourierCalls = CourierCall::where('request_service_id', $RequestService->id)->where('courier_id', $RequestService->request_currier_id)->get();
                // foreach ($variable as $key => $value) {
                //     # code...
                // }
                $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE request_service_id = '$RequestService->id' AND courier_id = '$request->courier_id' order by created_at asc";
                $courier_call_lists = DB::select($sql);
                $call_status = json_encode($courier_call_lists, JSON_UNESCAPED_UNICODE);

                $call_zero = 0;
                $call_one = 0;
                $call_two = 0;
                foreach ($courier_call_lists as $courier_call_list) {
                    if($courier_call_list->callstatus == 0){
                        $call_zero += 1;
                    }else if($courier_call_list->callstatus == 1){
                        $call_one += 1;
                    }else if($courier_call_list->callstatus == 2){
                        $call_two += 1;
                    }
                }
                if($call_zero >= 1){
                    $color_status = "blue";
                    $status = "พร้อมเข้ารับพัสดุ";
                }else if($call_one >= 1){
                    $color_status = "red";
                    $status = "ยกเลิการเข้ารับ/เบอร์ผิด";
                }else if($call_two >= 3){
                    $color_status = "red";
                    $status = "ติดปัญหา";
                }else if($call_two >= 1){
                    $color_status = "blue";
                    $status = "รอเข้ารับ";
                }else{
                    $color_status = "gray";
                    $status = "รอเข้ารับ";
                }

                return '{
                    "booking_id":"'.$Booking->id.'",
                    "booking_no":"'.$Booking->booking_no.'",
                    "status":"'.$status.'",
                    "color_status":"'.$color_status.'",
                    "sender":{
                        "cust_name":"'.$Booking->customer->cust_name.'",
                        "customer_phone":"'.$Booking->customer->cust_phone.'",
                        "customer_address":"'.$Booking->customer->cust_address.' '."ต.".''.$Booking->customer->District->name_th.' '."อ.".''.$Booking->customer->amphure->name_th.' '."อ.".''.$Booking->customer->province->name_th.'",
                        "customer_zipcode":"'.$Booking->customer->cust_postcode.'"
                    },
                    "call_history":'.$call_status.',
                    "problem":"'.$RequestService->action_status.'"
                }';
            }else{
                return '{"status":"0","msg":"error 322"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function count_request_recive(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'courier_id' => 'required'
            ]);

            if ($validator->fails()) {
                return '{"status":"0","msg":"error 331"}';
            }

            $RequestServices = RequestService::where('request_currier_id', $request->courier_id)->where('request_status', 'request')->whereDate('created_at', DB::raw('CURDATE()'))->get();

            return '{
                "request_count":"'.count($RequestServices).'"
            }';
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
}
