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
use App\Model\LoginStartDay;
use Validator;
use Hash;
use App\Http\Resources\UserResource;
use Auth;
use DB;
class ApplicationController extends Controller
{
    
    public function courierLogout(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            // dd($request->username);
            if ($request->courier_id) {
                $Employee = Employee::where('id',$request->courier_id)->where('emp_position','พนักงานจัดส่งพัสดุ(Courier)')->orwhere('id',$request->courier_id)->where('emp_position','พนักงานส่งพัสดุ(Line Haul)')->first();
                if($Employee){
                    $Employee->update(['courierstatus' => '1']);
                    return '
                            {
                                "status":"1",
                                "msg":"Logout Successfully"
                            }
                        ';
                }else{
                    return '{"status":"0","msg":"error 602"}';
                }
            }else{
                return '{"status":"0","msg":"error 601"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function courier_request_password(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if ($request->username) {
                $user = User::where('email',$request->username)->first();
                $Employee = Employee::where('id',$user->employee_id)->where('emp_position','พนักงานจัดส่งพัสดุ(Courier)')->orwhere('id',$user->employee_id)->where('emp_position','พนักงานส่งพัสดุ(Line Haul)')->first();
                if($Employee){
                    CourierRequestPassword::create([
                        'employee_id' => $Employee->id,
                        'emp_branch_id' => $Employee->emp_branch_id,
                        'status' => 'new'
                    ]);
                    return '{"status":"1","msg":"Success"}';
                }else{
                    return '{"status":"0","msg":"error 902"}';
                }
            }else{
                return '{"status":"0","msg":"error 901"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function check_online(Request $request){
        $user = user::where('email', $request->email)->first();
        if(Hash::check($request->password, $user->password)){
            if($user->isOnline()){
                return '{"status":"0"}';
            }
        }
        return '{"status":"1"}';
    }

    public function courierLogin(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if ($request->username && $request->password) {

                $username = $request->username;
                $password = $request->password;

                $user = User::where('email',$username)->first();
                $Employee = Employee::where('id',$user->employee_id)->where('emp_position','พนักงานจัดส่งพัสดุ(Courier)')->orwhere('id',$user->employee_id)->where('emp_position','พนักงานส่งพัสดุ(Line Haul)')->first();
                if($Employee){
                    $passwordUser = $user->password;
                    $passwordRequest = $password;

                    if(Hash::check($passwordRequest, $passwordUser)){
                        if($Employee->emp_position == 'พนักงานจัดส่งพัสดุ(Courier)'){
                            $user_type = 'courier';
                        }else if($Employee->emp_position == 'พนักงานส่งพัสดุ(Line Haul)'){
                            $user_type = 'linehaul';
                        }
                        CourierLogin::create([
                            'employee_id' => $Employee->id,
                            'login_status' => 0,
                            'courier_login_image' => null,
                            'branch_id' => $Employee->emp_branch_id,
                            'login_type' => $user_type,

                        ]);
                        // $Employee->update(['courierstatus' => '2']);
                        // dd($passwordUser);
                        // Auth::login($user, true);
                        // $user = Auth::user(); 
                        // $array['firstname'] = $user->name;
                        // $array['token'] = $user->createToken('kts_system')->accessToken;
                        $LoginStartDays = LoginStartDay::where('employee_id',$Employee->id)->whereDate('created_at', DB::raw('CURDATE()'))->first();
                        if(!empty($LoginStartDays)){
                            if($LoginStartDays->login_lat_long !== "" && $LoginStartDays->logout_lat_long == ""){
                                $check_in_status = '1';
                            }else if($LoginStartDays->login_lat_long !== "" && $LoginStartDays->logout_lat_long !== ""){
                                $check_in_status = '2';
                            }else{
                                $check_in_status = 'error';
                            }
                        }else{
                            $check_in_status = '0';
                        }
                        return '
                                {
                                    "status":"1",
                                    "user_type":"'.$user_type.'",
                                    "courier_id":"'.$Employee->id.'",
                                    "user":"'.$Employee->emp_firstname.' '.$Employee->emp_lastname.'",
                                    "check_in_status":"'.$check_in_status.'",
                                    "online_status":"'.$Employee->courierstatus.'"
                                }
                            ';
                    }else{
                        return '{"status":"0","msg":"error 103"}';
                    }
                }else{
                    return '{"status":"0","msg":"error 102"}';
                }
            }else{
                return '{"status":"0","msg":"error 101"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function courierLogin_send_img(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if ($request->courier_id && $request->courier_login_image && $request->lat_long) {
                $date = date('Y-m-d');
                $CourierLogin = CourierLogin::where('employee_id', $request->courier_id)->where('login_status', '0')->whereDate('created_at', DB::raw('CURDATE()'))->orderBy('created_at', 'desc')->first();
                // dd($CourierLogin);
                if($CourierLogin){
                    //DB::raw('CURDATE()')
                    $CourierLogin->update([
                        'login_status' => 1,
                        'courier_login_image' => $request->courier_login_image,
                        'lat_long' => $request->lat_long
                    ]);
                    $Employee = Employee::where('id',$request->courier_id)->where('emp_position','พนักงานจัดส่งพัสดุ(Courier)')->orwhere('id',$request->courier_id)->where('emp_position','พนักงานส่งพัสดุ(Line Haul)')->first();
                    $Employee->update(['courierstatus' => '2']);
                    return '{"status":"1","msg":"Success"}';
                }else{
                    return '{"status":"0","msg":"error 802"}';
                }
            }else{
                return '{"status":"0","msg":"error 801"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function check_in(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'courier_id' => 'required',
                'login_img' => 'required',
                'login_lat_long' => 'required'  
            ]);
            if ($validator->fails()) {
                return '{"status":"0","msg":"error 411"}';
            }

            $Employee = Employee::where('id',$request->courier_id)->where('emp_position','พนักงานจัดส่งพัสดุ(Courier)')->orwhere('id',$request->courier_id)->where('emp_position','พนักงานส่งพัสดุ(Line Haul)')->first();
            if(!empty($Employee)){
                $LoginStartDays = LoginStartDay::where('employee_id',$Employee->id)->whereDate('created_at', DB::raw('CURDATE()'))->first();
                if(!empty($LoginStartDays)){
                    if($LoginStartDays->login_lat_long !== "" && $LoginStartDays->logout_lat_long == ""){
                        return '{"status":"0","msg":"error 413"}';
                    }else if($LoginStartDays->login_lat_long !== "" && $LoginStartDays->logout_lat_long !== ""){
                        return '{"status":"0","msg":"error 414"}';
                    }else{
                        return '{"status":"0","msg":"error 415"}';
                    }
                }else{
                    $date = date('Y-m-d H:i:s');
                    $LoginStartDay = LoginStartDay::create([
                        'employee_id' => $Employee->id,
                        'login_img' => $request->login_img,
                        'login_lat_long' => $request->login_lat_long,
                        'login_time' => $date,
                        'branch_id' => $Employee->emp_branch_id
                    ]);

                    return '{"status":"1","msg":"success","check_in_status":"1"}';
                }
            }else{
                return '{"status":"0","msg":"error 412"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function check_out(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'courier_id' => 'required',
                'logout_img' => 'required',
                'logout_lat_long' => 'required'  
            ]);
            if ($validator->fails()) {
                return '{"status":"0","msg":"error 421"}';
            }

            $Employee = Employee::where('id',$request->courier_id)->where('emp_position','พนักงานจัดส่งพัสดุ(Courier)')->orwhere('id',$request->courier_id)->where('emp_position','พนักงานส่งพัสดุ(Line Haul)')->first();
            if(!empty($Employee)){
                $LoginStartDays = LoginStartDay::where('employee_id',$Employee->id)->whereDate('created_at', DB::raw('CURDATE()'))->first();
                if(!empty($LoginStartDays)){
                    if($LoginStartDays->login_lat_long !== "" && $LoginStartDays->logout_lat_long == ""){
                        $date = date('Y-m-d H:i:s');
                        $LoginStartDays->update([
                            "logout_img" => $request->logout_img,
                            "logout_lat_long" => $request->logout_lat_long,
                            "logout_time" => $date
                        ]);

                        return '{"status":"1","msg":"success","check_in_status":"2"}';
                    }else if($LoginStartDays->login_lat_long !== "" && $LoginStartDays->logout_lat_long !== ""){
                        // return '{"status":"0","msg":"error 424"}';
                        $date = date('Y-m-d H:i:s');
                        $LoginStartDays->update([
                            "logout_img" => $request->logout_img,
                            "logout_lat_long" => $request->logout_lat_long,
                            "logout_time" => $date
                        ]);
                        return '{"status":"1","msg":"success","check_in_status":"2"}';
                    }else{
                        return '{"status":"0","msg":"error 425"}';
                    }
                }else{
                    return '{"status":"0","msg":"error 423"}';
                }
            }else{
                return '{"status":"0","msg":"error 422"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function courier_tracking_list(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if ($request->courier_id){
                $TranserBill = TranserBill::where('transfer_bill_courier_id', $request->courier_id)->where('transfer_bill_status', 'TransferToCourier')->orwhere('transfer_bill_courier_id', $request->courier_id)->where('transfer_bill_status', 'sendingCOD')->first();
                $courier_tracking_list = '';
                if(!empty($TranserBill)){
                    $Transfer = Transfer::where('transfer_bill_id', $TranserBill->id)->where('transfer_status', 'TransferToCourier')->orwhere('transfer_bill_id', $TranserBill->id)->where('transfer_status', 'TransferToCourierReturn')->get();
                    if(count($Transfer) > 0){
                        $sql = "SELECT a.id, a.transfer_tracking_id, b.tracking_no, c.cust_name, c.cust_address, d.name_th , e.name_th as name_th1, f.name_th as name_th2, c.cust_postcode, c.cust_phone, a.transfer_status  FROM transfers a LEFT JOIN trackings b ON a.transfer_tracking_id = b.id LEFT JOIN customers c ON b.tracking_receiver_id = c.id LEFT JOIN districts d ON c.cust_sub_district = d.id LEFT JOIN amphures e ON c.cust_district = e.id LEFT JOIN provinces f ON c.cust_province = f.id WHERE a.transfer_courier_id = '$request->courier_id' AND a.transfer_status = 'TransferToCourier' OR a.transfer_courier_id = '$request->courier_id' AND a.transfer_status = 'TransferToCourierReturn'";
                        $tracking_lists = DB::select($sql);

                        $i = 0;
                        foreach ($tracking_lists as $tracking_list) {
                            $i++;
                            $call_zero = 0;
                            $call_one = 0;
                            $call_two = 0;
                            $color_status = "gray";
                            $redcolor = 0;
                            $sql = "SELECT count(id) numcount FROM courier_calls WHERE tracking_id = '$tracking_list->transfer_tracking_id' AND courier_id = '$request->courier_id' AND tranfer_id = '$tracking_list->id'";
                            $courier_call_count = DB::select($sql);
                            $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE tracking_id = '$tracking_list->transfer_tracking_id' AND courier_id = '$request->courier_id' AND tranfer_id = '$tracking_list->id' order by created_at asc";
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
                                if($courier_call_list->note == 'ปฏิเสธ รับพัสดุ' || $courier_call_list->note == 'เบอร์ผิด'){
                                    $redcolor = 1;
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

                            if($redcolor == 1){
                                $color_status = "red";
                            }
                            
                            $rtnshow = "";
                            if(strpos($tracking_list->transfer_status, 'Return') !== false){
                                $rtnshow = "(RTN)";
                            }
                            if($i == 1){
                                $courier_tracking_list .= '{
                                    "tranfer_id":"'.$tracking_list->id.'",
                                    "tracking_no":"'.$tracking_list->tracking_no.$rtnshow.'",
                                    "customer_name":"'.$tracking_list->cust_name.'",
                                    "customer_phone":"'.$tracking_list->cust_phone.'",
                                    "customer_address":"'.nl2br($tracking_list->cust_address).' '."ต.".''.$tracking_list->name_th.' '."อ.".''.$tracking_list->name_th1.' '."จ.".''.$tracking_list->name_th2.'",
                                    "customer_zipcode":"'.$tracking_list->cust_postcode.'",
                                    "color_status":"'.$color_status.'",
                                    "count_status":"'.$courier_call_count[0]->numcount.'",
                                    "call_status":	'.$call_status.'
                                }';
                            }else{
                                $courier_tracking_list .= ',{
                                    "tranfer_id":"'.$tracking_list->id.'",
                                    "tracking_no":"'.$tracking_list->tracking_no.$rtnshow.'",
                                    "customer_name":"'.$tracking_list->cust_name.'",
                                    "customer_phone":"'.$tracking_list->cust_phone.'",
                                    "customer_address":"'.nl2br($tracking_list->cust_address).' '."ต.".''.$tracking_list->name_th.' '."อ.".''.$tracking_list->name_th1.' '."จ.".''.$tracking_list->name_th2.'",
                                    "customer_zipcode":"'.$tracking_list->cust_postcode.'",
                                    "color_status":"'.$color_status.'",
                                    "count_status":"'.$courier_call_count[0]->numcount.'",
                                    "call_status":	'.$call_status.'
                                }';
                            }
                        }
                    }else{
                        $courier_tracking_list = '{
                            "tranfer_id": "Closing_job"
                        }';
                    }
                }
                $courier_tracking_list = '['.$courier_tracking_list.']';
                
                return $courier_tracking_list;
            }else{
                return '{"status":"0","msg":"error 201"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function tracking_detail(Request $Request){
        if ($Request->Secure && $Request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($Request->tracking_no){
                $tracking = substr($Request->tracking_no, 0, 15);
                $sql = "SELECT a.id, b.id as trackid, b.tracking_no, c.cust_name, c.cust_address, d.name_th , e.name_th as name_th1, f.name_th as name_th2, c.cust_postcode, c.cust_phone, a.transfer_status, b.tracking_amount FROM transfers a LEFT JOIN trackings b ON a.transfer_tracking_id = b.id LEFT JOIN customers c ON b.tracking_receiver_id = c.id LEFT JOIN districts d ON c.cust_sub_district = d.id LEFT JOIN amphures e ON c.cust_district = e.id LEFT JOIN provinces f ON c.cust_province = f.id WHERE b.tracking_no = '$tracking' AND a.transfer_status = 'TransferToCourier' AND a.transfer_courier_id = '$Request->courier_id' OR b.tracking_no = '$tracking' AND a.transfer_status = 'TransferToCourierReturn' AND a.transfer_courier_id = '$Request->courier_id'";
                $tracking_detail = DB::select($sql);

                if(!empty($tracking_detail)){
                    $track_id = $tracking_detail[0]->trackid;

                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $track_id)->get();
                    $cod = 0;
                    $rtnshow = "";
                    foreach ($SubTrackings as $SubTracking) {
                        if(strpos($tracking_detail[0]->transfer_status, 'Return') !== false){
                            $rtnshow = "(RTN)";
                            $cod += $SubTracking->subtracking_price;
                        }else{
                            $cod += $SubTracking->subtracking_cod;
                        }
                    }

                    return '{
                        "tranfer_id":"'.$tracking_detail[0]->id.'",
                        "tracking_no":"'.$tracking_detail[0]->tracking_no.$rtnshow.'",
                        "customer_name":"'.$tracking_detail[0]->cust_name.'",
                        "customer_phone":"'.$tracking_detail[0]->cust_phone.'",
                        "customer_address":"'.$tracking_detail[0]->cust_address.' '."ต.".''.$tracking_detail[0]->name_th.' '."อ.".''.$tracking_detail[0]->name_th1.' '."อ.".''.$tracking_detail[0]->name_th2.'",
                        "customer_zipcode":"'.$tracking_detail[0]->cust_postcode.'",
                        "cod":"'.$cod.'",
                        "box_amount":"'.count($SubTrackings).'"
                    }';
                }else{
                    return '{"status":"0","msg":"error 302"}';
                }
            }else{
                return '{"status":"0","msg":"error 301"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function tracking_detail_call_detail(Request $Request) {
        if ($Request->Secure && $Request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($Request->tracking_no){
                $tracking = substr($Request->tracking_no, 0, 15);
                $sql = "SELECT b.tracking_booking_id, a.id, a.transfer_courier_id, b.id as trackId, b.tracking_no, c.cust_name, c.cust_address, d.name_th , e.name_th as name_th1, f.name_th as name_th2, c.cust_postcode, c.cust_phone, a.transfer_status FROM transfers a LEFT JOIN trackings b ON a.transfer_tracking_id = b.id LEFT JOIN customers c ON b.tracking_receiver_id = c.id LEFT JOIN districts d ON c.cust_sub_district = d.id LEFT JOIN amphures e ON c.cust_district = e.id LEFT JOIN provinces f ON c.cust_province = f.id WHERE b.tracking_no = '$tracking' AND a.transfer_status = 'TransferToCourier' OR b.tracking_no = '$tracking' AND a.transfer_status = 'TransferToCourierReturn'";
                $tracking_detail = DB::select($sql);

                if(!empty($tracking_detail)){
                    $tracking_id = $tracking_detail[0]->trackId;
                    $transfer_courier_id = $tracking_detail[0]->transfer_courier_id;
                    $tranfer_id = $tracking_detail[0]->id;

                    $Booking = Booking::find($tracking_detail[0]->tracking_booking_id);

                    // $date = date('Y-m-d');
                    $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE tracking_id = '$tracking_id' AND courier_id = '$transfer_courier_id' AND tranfer_id = '$tranfer_id' order by created_at asc";
                    $courier_call_lists = DB::select($sql);
                    $call_status = json_encode($courier_call_lists, JSON_UNESCAPED_UNICODE);

                    $call_zero = 0;
                    $call_one = 0;
                    $call_two = 0;
                    $redcolor = 0;
                    foreach ($courier_call_lists as $courier_call_list) {
                        if($courier_call_list->callstatus == 0){
                            $call_zero += 1;
                        }else if($courier_call_list->callstatus == 1){
                            $call_one += 1;
                        }else if($courier_call_list->callstatus == 2){
                            $call_two += 1;
                        }

                        if($courier_call_list->note == 'ปฏิเสธ รับพัสดุ' || $courier_call_list->note == 'เบอร์ผิด'){
                            $redcolor = 1;
                        }
                    }
                    if($redcolor == 1){
                        $color_status = "red";
                        $status = "ติดปัญหา";
                    }else if($call_zero >= 1){
                        $color_status = "blue";
                        $status = "รอนำส่ง";
                    }else if($call_one >= 1){
                        $color_status = "red";
                        $status = "เลื่อนรับ";
                    }else if($call_two >= 3){
                        $color_status = "red";
                        $status = "ติดปัญหา";
                    }else if($call_two >= 1){
                        $color_status = "blue";
                        $status = "รอจัดส่ง";
                    }else{
                        $color_status = "gray";
                        $status = "รอจัดส่ง";
                    }

                    $rtnshow = "";
                    if(strpos($tracking_detail[0]->transfer_status, 'Return') !== false){
                        $rtnshow = "(RTN)";
                    }

                    return '{
                        "tranfer_id":"'.$tracking_detail[0]->id.'",
                        "tracking_no":"'.$tracking_detail[0]->tracking_no.$rtnshow.'",
                        "status":"'.$status.'",
                        "color_status":"'.$color_status.'",
                        "sender":{
                            "cust_name":"'.$Booking->customer->cust_name.'",
                            "customer_phone":"'.$Booking->customer->cust_phone.'",
                            "customer_address":"'.$Booking->customer->cust_address.' '."ต.".''.$Booking->customer->District->name_th.' '."อ.".''.$Booking->customer->amphure->name_th.' '."อ.".''.$Booking->customer->province->name_th.'",
                            "customer_zipcode":"'.$Booking->customer->cust_postcode.'"
                        },
                        "receive":{
                            "customer_name":"'.$tracking_detail[0]->cust_name.'",
                            "customer_phone":"'.$tracking_detail[0]->cust_phone.'",
                            "customer_address":"'.$tracking_detail[0]->cust_address.' '."ต.".''.$tracking_detail[0]->name_th.' '."อ.".''.$tracking_detail[0]->name_th1.' '."อ.".''.$tracking_detail[0]->name_th2.'",
                            "customer_zipcode":"'.$tracking_detail[0]->cust_postcode.'"
                        },
                        "call_history":'.$call_status.'
                    }';
                }else{
                    return '{"status":"0","msg":"error 122"}';
                }
            }else{
                return '{"status":"0","msg":"error 121"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function courier_tracking_list_success(Request $request) {
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if ($request->courier_id){
                $date = date('Y-m-d');
                $sql = "SELECT g.transfer_bill_status, a.id, a.transfer_tracking_id, b.tracking_no, c.cust_name, c.cust_address, d.name_th , e.name_th as name_th1, f.name_th as name_th2, c.cust_postcode, c.cust_phone, a.transfer_status FROM transfers a LEFT JOIN transer_bills g ON g.id = a.transfer_bill_id LEFT JOIN trackings b ON a.transfer_tracking_id = b.id LEFT JOIN customers c ON b.tracking_receiver_id = c.id LEFT JOIN districts d ON c.cust_sub_district = d.id LEFT JOIN amphures e ON c.cust_district = e.id LEFT JOIN provinces f ON c.cust_province = f.id WHERE a.transfer_courier_id = '$request->courier_id' AND a.transfer_status = 'CustomerResiveDone' AND g.transfer_bill_status != 'done' OR a.transfer_courier_id = '$request->courier_id' AND a.transfer_status = 'CustomerResiveDone' AND a.created_at like '$date%' OR a.transfer_courier_id = '$request->courier_id' AND a.transfer_status = 'CustomerResiveDoneReturn' AND g.transfer_bill_status != 'done' OR a.transfer_courier_id = '$request->courier_id' AND a.transfer_status = 'CustomerResiveDoneReturn' AND a.created_at like '$date%'";
                $tracking_lists = DB::select($sql);

                $courier_tracking_list = '';
                $i = 0;
                foreach ($tracking_lists as $tracking_list) {
                    $rtnshow = "";
                    if(strpos($tracking_list->transfer_status, 'Return') !== false){
                        $rtnshow = "(RTN)";
                    }

                    $tranfer_id = $tracking_list->id;
                    $i++;
                    $call_zero = 0;
                    $call_one = 0;
                    $call_two = 0;
                    $color_status = "gray";
                    $sql = "SELECT count(id) numcount FROM courier_calls WHERE tracking_id = '$tracking_list->transfer_tracking_id' AND tranfer_id = '$tranfer_id'";
                    $courier_call_count = DB::select($sql);
                    // $date = date('Y-m-d');  AND created_at LIKE '$date%'
                    $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE tracking_id = '$tracking_list->transfer_tracking_id' AND tranfer_id = '$tranfer_id'";
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

                    if($i == 1){
                        $courier_tracking_list .= '{
                            "tranfer_id":"'.$tracking_list->id.'",
                            "tracking_no":"'.$tracking_list->tracking_no.$rtnshow.'",
                            "customer_name":"'.$tracking_list->cust_name.'",
                            "customer_phone":"'.$tracking_list->cust_phone.'",
                            "customer_address":"'.$tracking_list->cust_address.' '."ต.".''.$tracking_list->name_th.' '."อ.".''.$tracking_list->name_th1.' '."จ.".''.$tracking_list->name_th2.'",
                            "customer_zipcode":"'.$tracking_list->cust_postcode.'",
                            "color_status":"'.$color_status.'",
                            "count_status":"'.$courier_call_count[0]->numcount.'",
                            "call_status":	'.$call_status.'
                        }';
                    }else{
                        $courier_tracking_list .= ',{
                            "tranfer_id":"'.$tracking_list->id.'",
                            "tracking_no":"'.$tracking_list->tracking_no.$rtnshow.'",
                            "customer_name":"'.$tracking_list->cust_name.'",
                            "customer_phone":"'.$tracking_list->cust_phone.'",
                            "customer_address":"'.$tracking_list->cust_address.' '."ต.".''.$tracking_list->name_th.' '."อ.".''.$tracking_list->name_th1.' '."จ.".''.$tracking_list->name_th2.'",
                            "customer_zipcode":"'.$tracking_list->cust_postcode.'",
                            "color_status":"'.$color_status.'",
                            "count_status":"'.$courier_call_count[0]->numcount.'",
                            "call_status":	'.$call_status.'
                        }';
                    }
                }
                $courier_tracking_list = '['.$courier_tracking_list.']';
                
                return $courier_tracking_list;
            }else{
                return '{"status":"0","msg":"error 701"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function tracking_success_detail(Request $Request){
        if ($Request->Secure && $Request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($Request->tracking_no){
                $tracking = substr($Request->tracking_no, 0, 15);
                $sql = "SELECT a.photo, a.signature, a.receive_name, a.receive_relation, a.id, b.tracking_no, c.cust_name, c.cust_address, d.name_th , e.name_th as name_th1, f.name_th as name_th2, c.cust_postcode, c.cust_phone, a.transfer_status FROM transfers a LEFT JOIN trackings b ON a.transfer_tracking_id = b.id LEFT JOIN customers c ON b.tracking_receiver_id = c.id LEFT JOIN districts d ON c.cust_sub_district = d.id LEFT JOIN amphures e ON c.cust_district = e.id LEFT JOIN provinces f ON c.cust_province = f.id WHERE b.tracking_no = '$tracking' AND a.transfer_status = 'CustomerResiveDone' OR b.tracking_no = '$tracking' AND a.transfer_status = 'CustomerResiveDoneReturn'";
                $tracking_detail = DB::select($sql);

                if(!empty($tracking_detail)){
                    $rtnshow = "";
                    if(strpos($tracking_detail[0]->transfer_status, 'Return') !== false){
                        $rtnshow = "(RTN)";
                    }
                    $str = preg_replace("/[\r\n]*/","",$tracking_detail[0]->signature);
                    return '{
                        "tranfer_id":"'.$tracking_detail[0]->id.'",
                        "tracking_no":"'.$tracking_detail[0]->tracking_no.$rtnshow.'",
                        "customer_name":"'.$tracking_detail[0]->cust_name.'",
                        "customer_phone":"'.$tracking_detail[0]->cust_phone.'",
                        "customer_address":"'.$tracking_detail[0]->cust_address.' '."ต.".''.$tracking_detail[0]->name_th.' '."อ.".''.$tracking_detail[0]->name_th1.' '."อ.".''.$tracking_detail[0]->name_th2.'",
                        "customer_postcode":"'.$tracking_detail[0]->cust_postcode.'",
                        "receive_photo":"'.$tracking_detail[0]->photo.'",
                        "receive_signature":"'.$str.'",
                        "receive_name":"'.$tracking_detail[0]->receive_name.'",
                        "receive_relation":"'.$tracking_detail[0]->receive_relation.'"
                    }';
                }else{
                    return '{"status":"0","msg":"error 112"}';
                }
            }else{
                return '{"status":"0","msg":"error 111"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }


    public function courier_call_status(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->tracking_no && $request->callstatus != ""){
                $request->tracking_no = substr($request->tracking_no, 0, 15);
                $Tracking = Tracking::where('tracking_no', $request->tracking_no)->first();
                
                if(!empty($Tracking)){
                    $Transfer = Transfer::where('transfer_tracking_id', $Tracking->id)->orderby('created_at','desc')->first();
                    if(!empty($Transfer)){
                        $time = date('Y-m-d H:i:s');
                        $CourierCall = CourierCall::create([
                            'tracking_id' => $Transfer->transfer_tracking_id,
                            'tranfer_id' => $Transfer->id,
                            'courier_id' => $request->courier_id,
                            'callstatus' => $request->callstatus,
                            'pick_time' => $request->pick_time,
                            'note' => $request->note, 
                            'oncall' => $request->oncall, 
                            'ontalk' => $request->ontalk, 
                            'callTime' => $time
                        ]);
                        
                        if($request->callstatus == 1){
                            $Tracking->update([
                                "tracking_send_status" => "postpone",
                                "send_pick_time" => $request->pick_time
                            ]);
                        }

                        if($request->callstatus == 0){
                            $Transfer->update([
                                "count_call" => $Transfer->count_call+1,
                                "recive_admit" => 'sending'
                            ]);
                        }else{
                            $Transfer->update([
                                "count_call" => $Transfer->count_call+1
                            ]);
                        }

                        $date = date('Y-m-d');
                        $PacelCare_status = 8;
                        if (strpos($Transfer->transfer_status, 'Return') !== false) {
                            $PacelCare_status = 19;
                        }
                        $PacelCare = PacelCare::where('tracking_id', $Transfer->transfer_tracking_id)->where('status', $PacelCare_status)->where('ref_no', $Transfer->id)->where('created_at','like', $date.'%')->first();
                        if(empty($PacelCare)){
                            $PacelCare = PacelCare::create([
                                'tracking_id' => $Transfer->transfer_tracking_id, 
                                'doing_by' => $Transfer->transfer_courier_id,
                                'branch_id' => $Transfer->transfer_branch_id, 
                                'status' => $PacelCare_status, 
                                'ref_no' => $Transfer->id
                            ]);
                        }

                        $sql = "SELECT a.id, a.transfer_tracking_id, b.tracking_no, c.cust_name, c.cust_address, d.name_th , e.name_th as name_th1, f.name_th as name_th2, c.cust_postcode, c.cust_phone, a.transfer_status FROM transfers a LEFT JOIN trackings b ON a.transfer_tracking_id = b.id LEFT JOIN customers c ON b.tracking_receiver_id = c.id LEFT JOIN districts d ON c.cust_sub_district = d.id LEFT JOIN amphures e ON c.cust_district = e.id LEFT JOIN provinces f ON c.cust_province = f.id WHERE a.transfer_courier_id = '$Transfer->transfer_courier_id' AND a.transfer_status = 'TransferToCourier' OR a.transfer_courier_id = '$Transfer->transfer_courier_id' AND a.transfer_status = 'TransferToCourierReturn'";
                        $tracking_lists = DB::select($sql);
            
                        $courier_tracking_list = '';
                        $i = 0;
                        foreach ($tracking_lists as $tracking_list) {
                            $i++;
                            $call_zero = 0;
                            $call_one = 0;
                            $call_two = 0;
                            $color_status = "gray";
                            $redcolor = 0;
                            $sql = "SELECT count(id) numcount FROM courier_calls WHERE tracking_id = '$tracking_list->transfer_tracking_id' AND courier_id = '$request->courier_id' AND tranfer_id = '$tracking_list->id'";
                            $courier_call_count = DB::select($sql);
                            $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE tracking_id = '$tracking_list->transfer_tracking_id' AND courier_id = '$request->courier_id' AND tranfer_id = '$tracking_list->id' order by created_at asc";
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
                                if($courier_call_list->note == 'ปฏิเสธ รับพัสดุ' || $courier_call_list->note == 'เบอร์ผิด'){
                                    $redcolor = 1;
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

                            if($redcolor == 1){
                                $color_status = "red";
                            }
                            
                            $rtnshow = "";
                            if(strpos($tracking_list->transfer_status, 'Return') !== false){
                                $rtnshow = "(RTN)";
                            }
                            if($i == 1){
                                $courier_tracking_list .= '{
                                    "tranfer_id":"'.$tracking_list->id.'",
                                    "tracking_no":"'.$tracking_list->tracking_no.$rtnshow.'",
                                    "customer_name":"'.$tracking_list->cust_name.'",
                                    "customer_phone":"'.$tracking_list->cust_phone.'",
                                    "customer_address":"'.$tracking_list->cust_address.' '."ต.".''.$tracking_list->name_th.' '."อ.".''.$tracking_list->name_th1.' '."จ.".''.$tracking_list->name_th2.'",
                                    "customer_zipcode":"'.$tracking_list->cust_postcode.'",
                                    "color_status":"'.$color_status.'",
                                    "count_status":"'.$courier_call_count[0]->numcount.'",
                                    "call_status":	'.$call_status.'
                                }';
                            }else{
                                $courier_tracking_list .= ',{
                                    "tranfer_id":"'.$tracking_list->id.'",
                                    "tracking_no":"'.$tracking_list->tracking_no.$rtnshow.'",
                                    "customer_name":"'.$tracking_list->cust_name.'",
                                    "customer_phone":"'.$tracking_list->cust_phone.'",
                                    "customer_address":"'.$tracking_list->cust_address.' '."ต.".''.$tracking_list->name_th.' '."อ.".''.$tracking_list->name_th1.' '."จ.".''.$tracking_list->name_th2.'",
                                    "customer_zipcode":"'.$tracking_list->cust_postcode.'",
                                    "color_status":"'.$color_status.'",
                                    "count_status":"'.$courier_call_count[0]->numcount.'",
                                    "call_status":	'.$call_status.'
                                }';
                            }
                        }
                        $courier_tracking_list = '['.$courier_tracking_list.']';
                        
                        return $courier_tracking_list;
                    }else{
                        return '{"status":"0","msg":"error 403"}';
                    }
                }else{
                    return '{"status":"0","msg":"error 402"}';
                }
            }else{
                return '{"status":"0","msg":"error 401"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function update_tranfer_status(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            // dd("sss");
            if($request->tracking_no && $request->signature && $request->status && $request->receive_name && $request->receive_relation){
                $request->tracking_no = substr($request->tracking_no, 0, 15);
                if($request->status == '1'){
                    $tranfer_status = 'CustomerResiveDone';
                    $tracking_status_id = '7';
                }else{
                    $tranfer_status = 'SendingFalse';
                    $tracking_status_id = '4';
                }

                $tracking = Tracking::where('tracking_no', $request->tracking_no)->first();
                $customerresive = Customer::find($tracking->tracking_receiver_id);
                $PostCode = PostCode::where('postcode', $customerresive->cust_postcode)->first();
                $Transfer = Transfer::where('transfer_tracking_id', $tracking->id)->where('transfer_status', 'TransferToCourier')->orwhere('transfer_tracking_id', $tracking->id)->where('transfer_status', 'TransferToCourierReturn')->first();
                // $Transfer = Transfer::find($request->tranfer_id);
                if(!empty($Transfer)){
                    $rtnshow = "";
                    if(strpos($Transfer->transfer_status, 'Return') !== false){
                        $rtnshow = "(RTN)";
                        $tranfer_status .= "Return";
                    }
                    if($request->status == '1'){
                        $Transfer->update([
                            'transfer_status' => $tranfer_status,
                            'photo' => $request->photo,
                            'signature' => $request->signature,
                            'receive_name' => $request->receive_name,
                            'receive_relation' => $request->receive_relation
                        ]);

                        $tracking->update([
                            'tracking_status' => $tranfer_status
                        ]);
                    }else{
                        $Transfer->update([
                            'transfer_status' => $tranfer_status
                        ]);
                    }

                    $date = date('Y-m-d H:i:s');
                    $tracking_Log_status = 7;
                    $PacelCare_status = 9;
                    if (strpos($Transfer->transfer_status, 'Return') !== false) {
                        $tracking_Log_status = 13;
                        $PacelCare_status = 20;
                    }
                    
                    $TrackingsLogs = TrackingsLog::create([
                        'tracking_no' => $request->tracking_no.$rtnshow, 
                        'tracking_receiver_id' => $tracking->tracking_receiver_id,
                        'tracking_status_id' => $tracking_Log_status, 
                        'tracking_branch_id_dc' => $tracking->transfer_recriver_id, 
                        'tracking_branch_id_sub_dc' => $PostCode->drop_center_id,
                        'tracking_date' => $date
                    ]);
                    
                    $date = date('Y-m-d');
                    $PacelCare = PacelCare::where('tracking_id', $Transfer->transfer_tracking_id)->where('status', $PacelCare_status)->where('created_at','like', $date.'%')->first();
                    if(empty($PacelCare)){
                        $PacelCare = PacelCare::create([
                            'tracking_id' => $Transfer->transfer_tracking_id, 
                            'doing_by' => $Transfer->transfer_courier_id,
                            'branch_id' => $Transfer->transfer_branch_id, 
                            'status' => $PacelCare_status, 
                            'ref_no' => $Transfer->id
                        ]);
                    }

                    $sql = "SELECT a.photo, a.signature, a.receive_name, a.receive_relation, a.id, b.tracking_no, c.cust_name, c.cust_address, d.name_th , e.name_th as name_th1, f.name_th as name_th2, c.cust_postcode, c.cust_phone  FROM transfers a LEFT JOIN trackings b ON a.transfer_tracking_id = b.id LEFT JOIN customers c ON b.tracking_receiver_id = c.id LEFT JOIN districts d ON c.cust_sub_district = d.id LEFT JOIN amphures e ON c.cust_district = e.id LEFT JOIN provinces f ON c.cust_province = f.id WHERE b.tracking_no = '$request->tracking_no' ORDER BY a.created_at DESC LIMIT 1";
                    $tracking_detail = DB::select($sql);

                    return '{
                        "tranfer_id":"'.$tracking_detail[0]->id.'",
                        "tracking_no":"'.$tracking_detail[0]->tracking_no.$rtnshow.'",
                        "customer_name":"'.$tracking_detail[0]->cust_name.'",
                        "customer_phone":"'.$tracking_detail[0]->cust_phone.'",
                        "customer_address":"'.$tracking_detail[0]->cust_address.' '."ต.".''.$tracking_detail[0]->name_th.' '."อ.".''.$tracking_detail[0]->name_th1.' '."อ.".''.$tracking_detail[0]->name_th2.'",
                        "receive_photo":"'.$tracking_detail[0]->photo.'",
                        "receive_signature":"'.$tracking_detail[0]->signature.'",
                        "receive_name":"'.$tracking_detail[0]->receive_name.'",
                        "receive_relation":"'.$tracking_detail[0]->receive_relation.'"
                    }';
                }else{
                    return '{"status":"0","msg":"error 502"}';
                }
            }else{
                return '{"status":"0","msg":"error 501"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function courier_Closing_job_list(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->courier_id){
                $date = date('Y-m-d');
                $TranserBill = TranserBill::where('transfer_bill_courier_id', $request->courier_id)->where('transfer_bill_status', 'TransferToCourier')->orwhere('transfer_bill_courier_id', $request->courier_id)->where('transfer_bill_status', 'sendingCOD')->first();
                if(!empty($TranserBill)){
                    $sql = "SELECT b.tracking_no as tracking_no, a.cod_amount as cod, a.transfer_status FROM transfers a left join trackings b on a.transfer_tracking_id = b.id WHERE a.transfer_bill_id = '$TranserBill->id' AND a.transfer_status = 'TransferToCourier' OR a.transfer_bill_id = '$TranserBill->id' AND a.transfer_status = 'TransferToCourierReturn'";
                    $Transfers = DB::select($sql);
                    if(count($Transfers) > 0){
                        return '{"status":"0","msg":"error 134"}';
                    }else{
                        $sql = "SELECT IF(a.transfer_status like '%Return', concat(b.tracking_no, '(RTN)'), b.tracking_no) as tracking_no, IF(a.transfer_status like '%Return', b.tracking_amount, a.cod_amount) as cod, a.transfer_status FROM transfers a left join trackings b on a.transfer_tracking_id = b.id WHERE a.transfer_bill_id = '$TranserBill->id' AND a.transfer_status = 'CustomerResiveDone' OR a.transfer_bill_id = '$TranserBill->id' AND a.transfer_status = 'CustomerResiveDoneReturn'";
                        $Transfers = DB::select($sql);
                        $Transfer_json = json_encode($Transfers, JSON_UNESCAPED_UNICODE);
                        $cod_amount = 0;
                        $transfer_status = 1;
                        foreach ($Transfers as $Transfer) {
                            $cod_amount += $Transfer->cod;
                            if($Transfer->transfer_status == 'TransferToCourier' || $Transfer->transfer_status == 'TransferToCourierReturn'){
                                $transfer_status = 0;
                            }
                        }

                        if($transfer_status == '1'){
                            $json = '{
                                "tracking":'.$Transfer_json.',
                                "count_tracking":"'.count($Transfers).'",
                                "cod_amount":"'.number_format($cod_amount,2).'",
                                "jobstatus":"'.$TranserBill->transfer_bill_status.'"
                            }';
                        }else{
                            $json = '{"status":"0","msg":"error 133"}';
                        }

                        return $json;
                    }
                }else{
                    return '{"status":"0","msg":"error 132"}';
                }
            }else{
                return '{"status":"0","msg":"error 131"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function courier_Closing_job(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->courier_id){
                // $date = date('Y-m-d');
                $TranserBill = TranserBill::where('transfer_bill_courier_id', $request->courier_id)->where('transfer_bill_status', 'TransferToCourier')->first();
                if(!empty($TranserBill)){
                    
                    $sql = "SELECT IF(a.transfer_status like '%Return', concat(b.tracking_no, '(RTN)'), b.tracking_no) as tracking_no, IF(a.transfer_status like '%Return', b.tracking_amount, a.cod_amount) as cod, a.transfer_status FROM transfers a left join trackings b on a.transfer_tracking_id = b.id WHERE a.transfer_bill_id = '$TranserBill->id' AND a.transfer_status = 'CustomerResiveDone' OR a.transfer_bill_id = '$TranserBill->id' AND a.transfer_status = 'CustomerResiveDoneReturn'";
                    $Transfers = DB::select($sql);
                    $Transfer_json = json_encode($Transfers, JSON_UNESCAPED_UNICODE);
                    $cod_amount = 0;
                    $transfer_status = 1;
                    foreach ($Transfers as $Transfer) {
                        $cod_amount += $Transfer->cod;
                        if($Transfer->transfer_status == 'TransferToCourier' || $Transfer->transfer_status == 'TransferToCourierReturn'){
                            $transfer_status = 0;
                        }
                    }

                    if($transfer_status == '1'){
                        if($cod_amount == '0'){
                            $TranserBill->update([
                                'transfer_bill_status' => 'done',
                                'tranfer_closing_by_employee_id' => $TranserBill->transfer_bill_courier_id
                            ]);
                        }else{
                            $TranserBill->update([
                                'transfer_bill_status' => 'sendingCOD'
                            ]);
                        }
                        $json = '{
                            "tracking":'.$Transfer_json.',
                            "count_tracking":"'.count($Transfers).'",
                            "cod_amount":"'.number_format($cod_amount,2).'",
                            "jobstatus":"'.$TranserBill->transfer_bill_status.'"
                        }';
                    }else{
                        $json = '{"status":"0","msg":"error 143"}';
                    }

                    return $json;
                }else{
                    return '{"status":"0","msg":"error 142"}';
                }
            }else{
                return '{"status":"0","msg":"error 141"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function linehaul_bill(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            // dd($request->all());
            if($request->courier_id){
                $Employee = Employee::where('id',$request->courier_id)->where('emp_position','พนักงานส่งพัสดุ(Line Haul)')->first();
                if(!empty($Employee)){
                    $date = date('Y-m-d');
                    $sql = "
                    SELECT
                        a.id AS id, 
                        a.transfer_bill_no AS bill_no, 
                        b.drop_center_name_initial as from_dc, 
                        c.drop_center_name_initial AS to_dc, 
                        d.emp_firstname AS dc_do_name,
                        a.transfer_bill_status AS status,
                        a.created_at
                    FROM
                        transfer_drop_center_bills a 
                        LEFT JOIN drop_centers b ON a.transfer_sender_id = b.id 
                        LEFT JOIN drop_centers c ON a.transfer_recriver_id = c.id 
                        LEFT JOIN employees d ON a.tranfer_employee_sender_id = d.id
                    where 
                        a.transfer_bill_status = 'sending'
                        AND a.tranfer_driver_sender_name = '$request->courier_id'
                        
                        OR a.updated_at like '$date%'
                        AND a.tranfer_driver_sender_name = '$request->courier_id'
                    ";
                    $Bill_lists = DB::select($sql);
                    if(count($Bill_lists) > 0){
                        $Bill_lists = json_encode($Bill_lists, JSON_UNESCAPED_UNICODE);
                        return $Bill_lists;
                    }else{
                        return '{"status":"0","msg":"error 153"}';
                    }
                }else{
                    return '{"status":"0","msg":"error 152"}';
                }
            }else{
                return '{"status":"0","msg":"error 151"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function linehaul_trackting_list(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->id){
                $sql = "
                    SELECT
                        IF(a.transfer_dropcenter_status like '%Return', concat(a.transfer_dropcenter_tracking_no, '(RTN)'), a.transfer_dropcenter_tracking_no) AS tracking_no,
                        a.parcel_amount AS box_amount,
                        a.transfer_dropcenter_status AS status
                    FROM
                        transfer_drop_centers a
                    where 
                        a.transfer_bill_id_ref = '$request->id'
                    ";
                    $Bill_tracking_lists = DB::select($sql);
                    if(count($Bill_tracking_lists) > 0){
                        $Bill_tracking_lists = json_encode($Bill_tracking_lists, JSON_UNESCAPED_UNICODE);
                        return $Bill_tracking_lists;
                    }else{
                        return '{"status":"0","msg":"error 163"}';
                    }
            }else{
                return '{"status":"0","msg":"error 161"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function Renew_password(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            if($request->courier_id && $request->new_Password){
                // bcrypt($request->password)
                    $User = User::where('employee_id', $request->courier_id)->first();
                    $Admin = Admin::where('employee_id', $request->courier_id)->first();
                    
                    $User->update([
                        'password' => bcrypt($request->new_Password)
                    ]);
                    
                    $Admin->update([
                        'password' => bcrypt($request->new_Password)
                    ]);
                    

                    return '{"status":"1","msg":"Success"}';
            }else{
                return '{"status":"0","msg":"error 351"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }

    public function get_profile(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'courier_id' => 'required'
            ]);
            if($validator->fails()) {
                return '{"status":"0","msg":"error 371"}';
            }  

            $sql = "SELECT b.id AS courier_id, b.emp_image AS emp_img, b.emp_firstname AS emp_firstname, b.emp_lastname AS emp_lastname, b.emp_phone AS emp_phone, a.email AS emp_mail FROM admins a LEFT JOIN employees b ON a.employee_id = b.id WHERE a.employee_id = '$request->courier_id' AND b.id = '$request->courier_id'";
            $query = DB::select($sql);
            $Profile_json = json_encode($query, JSON_UNESCAPED_UNICODE);

            return $Profile_json;
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
    
    public function update_profile(Request $request){
        if ($request->Secure && $request->Secure == "domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback") {
            $validator = Validator::make($request->all(), [
                'courier_id' => 'required',
                'emp_firstname' => 'required',
                'emp_lastname' => 'required',
                'emp_phone' => 'required',
                'emp_mail' => 'required'
            ]);
            if($validator->fails()) {
                return '{"status":"0","msg":"error 381"}';
            }
            
            $employee = Employee::find($request->courier_id);
            
            if($employee){
                if($request->emp_img != ""){
                    $employee->update([
                        'emp_firstname' => $request->emp_firstname,
                        'emp_lastname' => $request->emp_lastname,
                        'emp_phone' => $request->emp_phone,
                        'emp_image' => $request->emp_img
                    ]);
                }else{
                    $employee->update([
                        'emp_firstname' => $request->emp_firstname,
                        'emp_lastname' => $request->emp_lastname,
                        'emp_phone' => $request->emp_phone
                    ]);
                }

                $user = User::where('employee_id',$request->courier_id)->first();
                if($user){
                    if($user->email == $request->emp_mail){
                        $user->update([
                            'name' => $request->emp_firstname
                        ]);
                    }else{
                        $date = date('Y-m-d H:i:s');
                        $user->update([
                            'name' => $request->emp_firstname,
                            'email' => $request->emp_mail,
                            'email_verified_at' => $date,
                        ]);
                    }
                }

                $admin = Admin::where('employee_id',$request->courier_id)->first();
                if($admin){
                    if($admin->email == $request->emp_mail){
                        $admin->update([
                            'name' => $request->emp_firstname
                        ]);
                    }else{
                        $date = date('Y-m-d H:i:s');
                        $admin->update([
                            'name' => $request->emp_firstname,
                            'email' => $request->emp_mail,
                            'email_verified_at' => $date
                        ]);
                    }
                }
                
                $sql = "SELECT b.id AS courier_id, b.emp_image AS emp_img, b.emp_firstname AS emp_firstname, b.emp_lastname AS emp_lastname, b.emp_phone AS emp_phone, a.email AS emp_mail FROM admins a LEFT JOIN employees b ON a.employee_id = b.id WHERE a.employee_id = '$request->courier_id' AND b.id = '$request->courier_id'";
                $query = DB::select($sql);
                $Profile_json = json_encode($query, JSON_UNESCAPED_UNICODE);

                return $Profile_json;
            }else{
                return '{"status":"0","msg":"error 382"}';
            }
        }else{
            return  '{
                        "error":"0"
                    }';
        }
    }
}
