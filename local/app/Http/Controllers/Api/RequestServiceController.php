<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\RequestService;
use App\Model\ParcelType;
use App\Model\ParcelPrice;
use App\Model\ProductPrice;
use App\Model\Customer;
use App\Model\Tracking;
use App\Model\SubTracking;
use App\Model\DimensionHistory;
use App\Model\Booking;
use App\Http\Resources\RequestServiceResource;
use App\Http\Resources\RequestServiceDetailResource;
use App\Http\Resources\RequestServiceTrackingDetailResource;
use App\Http\Resources\RequestServiceAllTrackingDetailResource;
use Auth;
use Validator;

class RequestServiceController extends Controller {
    public function getRequestServiceList() {
        $user = Auth::user();
        if($user){
            $requestServiceList = RequestService::where('request_currier_id',$user->employee_id)->get();
            $requestServiceResource = RequestServiceResource::collection($requestServiceList);
            return $requestServiceResource;
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }

    public function getRequestServiceDetail($id = null) {
        if($id){
            $user = Auth::user();
            $requestServiceList = RequestService::where('request_booking_id',$id)->first();
            $requestServiceResource = new RequestServiceResource($requestServiceList);
                if($requestServiceResource){
                    return $requestServiceResource;
                }else{
                    return _res(false, null, 'ไม่พบข้อมูล', null);
                }
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }

    public function getParcelType() {
        $parcelType = ParcelType::get();
        if($parcelType){
            return $parcelType;
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }

    public function getProductPrice() {
        $productPrice = ProductPrice::get();
        if($productPrice){
            return $productPrice;
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }

    public function searchCustomer($id = null) {
        if($id){
            $user = Auth::user();
            if($user){
                $customers = Customer::where('cust_phone','like', '%'. $id.'%')->get();
                return $customers;
            }else{
                return _res(false, null, 'ไม่พบข้อมูล', null);
            }

        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }

    public function createCustomerFromMobile(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'cust_name' => 'required',
            'cust_address' => 'required',
            'cust_sub_district' => 'required',
            'cust_district' => 'required',
            'cust_province' => 'required',
            'cust_postcode' => 'required',
            'cust_phone' => 'required'    
        ]);

        if ($validator->fails()) { 
            $errors = $validator->errors();
            return _res(false, null, 'ข้อมูลไม่ครบ', null);
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

        if($customer){ 

            $checkTrackings = Tracking::where('tracking_booking_id',$id)
            ->where('tracking_status','new')
            ->get();
           
            if(count($checkTrackings)>0){ //มีรายการสร้างเปล่าไว้อยู่แล้ว  จะเป็นการupdate receiver id แทนการสร้างใหม่
                if(count($checkTrackings)==1){
                    foreach ($checkTrackings as $checkTracking) {
                        
                        $checkTracking->update([
                            'tracking_receiver_id' => $customer->id
                        ]);
                       
                    // return _res(true, null, 'บันทึกสำเร็จ', null);
                    return $checkTracking;
                    }

                }else{  // ถ้ามีมากกว่า1แสดงว่าเกิดการเพิ่มฟิลด์ผิดพลาด
                    return _res(false, null, 'เกิดการเพิ่มฟิลด์ผิดพลาด', null);
                }
            }else{
                $booking_id = $id;
                $booking = Booking::find($id);

                $date = date('Y-m-d');
                $track_row = Tracking::whereDate('created_at',$date)->get();
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
                $jobs_status = "new";
                $trackings = Tracking::create([
                    'tracking_no' => $tracking_no,
                    'tracking_booking_id' => $booking_id,
                    'tracking_receiver_id' => $customer->id,
                    'tracking_parcel_type' => '-',
                    'tracking_status' => $jobs_status
                ]);

                return _res(true, null, 'บันทึกข้อมูลสำเร็จ', null);
            }

        }else{
            return _res(false, null, 'เกิดข้อผิดพลาด', null);
        }
    }

    public function createTrackingWhenSelectCustomerId(Request $request, $id) {
        // return $id;
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'cust_id' => 'required'
        ]);

        if ($validator->fails()) { 
            $errors = $validator->errors();
            return _res(false, null, 'ข้อมูลไม่ครบ', null);
        }

        $checkTrackings = Tracking::where('tracking_booking_id',$id)
        ->where('tracking_status','new')
        ->get();
        
        if(count($checkTrackings)>0){ //มีรายการสร้างเปล่าไว้อยู่แล้ว  จะเป็นการupdate receiver id แทนการสร้างใหม่
            if(count($checkTrackings)==1){
                foreach ($checkTrackings as $checkTracking) {
                    $checkTracking->update([
                        'tracking_receiver_id' => $request->cust_id
                    ]);
                    return $checkTracking;
                }

            }else{  // ถ้ามีมากกว่า1แสดงว่าเกิดการเพิ่มฟิลด์ผิดพลาด
                return _res(false, null, 'เกิดการเพิ่มฟิลด์ผิดพลาด', null);
            }
        }else{
            $booking_id = $id;
            $booking = Booking::find($id);

            $date = date('Y-m-d');
            $track_row = Tracking::whereDate('created_at',$date)->get();
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
            $jobs_status = "new";

            $trackings = Tracking::create([
                'tracking_no' => $tracking_no,
                'tracking_booking_id' => $booking_id,
                'tracking_receiver_id' => $request->cust_id,
                'tracking_parcel_type' => '-',
                'tracking_status' => $jobs_status
            ]);

            return _res(true, null, 'บันทึกข้อมูลสำเร็จ', null);
        }
    }

    public function getRequestDetail($id) { //tracking id
        if($id){
            $tracking = Tracking::where('id',$id)->first();
            $requestServiceResource = new RequestServiceDetailResource($tracking);
            if($requestServiceResource){
                return $requestServiceResource;
            }else{
                return _res(false, null, 'ไม่พบข้อมูล', null);
            }
        }else{
            return _res(false, null, 'เกิดข้อผิดพลาด', null);
        }
    }

    public function updateParcelTypeAndDimension(Request $request, $id) {
        if($request->dimension_type_id){
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required',
                'tracking_id' => 'required',
                'dimension_weigth' => 'required',
                'parcel_type_id' => 'required'
            ]);

        }else{
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required',
                'tracking_id' => 'required',
                'dimension_length' => 'required',
                'dimension_hight' => 'required',
                'dimension_width' => 'required',
                'dimension_weigth' => 'required',
                'parcel_type_id' => 'required'
            ]);
        }

        if ($validator->fails()) { 
            $errors = $validator->errors();
            return _res(false, null, 'ข้อมูลไม่ครบ', null);
        }

        // สร้างsubTracking
        // สร้างdimension history
        $subTracking = SubTracking::get();
        $countRow = count($subTracking)+1;
        $subtracking_no = date("Ymd").$countRow;

        //ถ้ามี dimension_type_id เข้ามาแสดงว่าเลือกจากลิสต์
        if($request->dimension_type_id) {
            $subtracking_dimension_type = 1;
        }else{
            $subtracking_dimension_type = 2;
        }
        //หาdimensionเพื่อคำนวนราคา
        if($request->dimension_type_id){
            $productPrice = ProductPrice::where('id',$request->dimension_type_id)->first();

            $hight = $productPrice->product_hight;
            $length = $productPrice->product_length;
            $width = $productPrice->product_width;
            $product_dimension = $productPrice->product_dimension;
            $dimension_type_id = $request->dimension_type_id;

        }else{
            $hight = $request->dimension_hight;
            $length = $request->dimension_length;
            $width = $request->dimension_width;
            $product_dimension = $hight + $length + $width;
            $dimension_type_id = '-';
        }

        //คำนวนราคาจากdimension
        $parcelPrices = ParcelPrice::get();
        $datas = [];
        foreach ($parcelPrices as $parcelPrice) {
            if($parcelPrice->parcel_total_dimension >= $product_dimension) {
                $rankPrice = $parcelPrice->parcel_price;
                $datas[] = $rankPrice;
            }
        }
        $dimension_price = min($datas); //ราคาจาก dimension
        $weigth = $request->dimension_weigth;
        $wdatas = [];
        foreach ($parcelPrices as $parcelPrice) {
            if($parcelPrice->parcel_total_weight >= $weigth) {

                $weigth_rankPrice = $parcelPrice->parcel_price;
                $wdatas[] = $weigth_rankPrice;
            }
        }
        $weigth_price = min($wdatas); //ราคาจากน้ำหนัก
        $dimension_price > $weigth_price ? $count_parcel_price = $dimension_price : $count_parcel_price = $weigth_price;
        $subTracking = SubTracking::create([
            'subtracking_no' => $subtracking_no,
            'subtracking_booking_id' => $request->booking_id,
            'subtracking_tracking_id' => $request->tracking_id,
            'subtracking_dimension_type' => $subtracking_dimension_type,
            'subtracking_cod' => $request->cod_amount,
            'subtracking_price' => $count_parcel_price,
            'subtracking_status' => "new",
            'subtracking_parcel_type' => $request->parcel_type_id

        ]);
        if($subTracking){
             // ต้องวิ่งเอาdimensionไปเก็บด้วย
            $dimensionHistory = DimensionHistory::create([
                'dimension_history_tracking_id' => $request->tracking_id,
                'dimension_history_subtracking_id' => $subTracking->id,
                'dimension_history_width' => $width,
                'dimension_history_hight' => $hight,
                'dimension_history_length' => $length,
                'dimension_history_total_dimension' => $product_dimension,
                'dimension_history_weigth' => $request->dimension_weigth,
                'dimension_history_status' => 'done',
            ]);

            $amountTotals = SubTracking::where('subtracking_tracking_id',$request->tracking_id)->get();
            $amount = 0;
            foreach($amountTotals as $amountTotal){
                $amount += $amountTotal->subtracking_price;
            }
            $tracking = Tracking::where('id',$request->tracking_id)->first();
            $tracking->update([
                'tracking_amount' => $amount
            ]);

            if($tracking){
                return _res(true, null, 'บันทึกข้อมูลสำเร็จ', null);
            }else{
                return _res(false, null, 'เกิดข้อผิดพลาด กระบวนการบันทึกข้อมูล dimension กรุณาตรวจสอบ', null);
            }
        }else{
            return _res(false, null, 'ไม่สามารถสร้างรายการพัสดุย่อยนี้ได้', null);
        }
    }

    public function getRequestServiceTrackingDetail($id = null) {
        if($id) {
            $tracking = Tracking::find($id);
            $requestServiceTrackingDetailResource = new RequestServiceTrackingDetailResource($tracking);
            return $requestServiceTrackingDetailResource;
        }else{
            return _res(false, null, 'เกิดข้อผิดพลาด', null);
        }
    }

    public function getRequestServiceAllTrackingDetail($id = null) {
        // return $id;
        if($id) {
            $booking = Booking::find($id);
            $requestServiceAllTrackingDetailResource = new RequestServiceAllTrackingDetailResource($booking);
            return $requestServiceAllTrackingDetailResource;
        }else{
            return _res(false, null, 'เกิดข้อผิดพลาด', null);
        }
    }

    public function deleteSubTracking($id = null) {
        if($id){
            $subTracking = SubTracking::where('id',$id)->first();
            $dimensionHistory = DimensionHistory::where('dimension_history_subtracking_id',$id)->first();
            $subTracking->delete();
            $dimensionHistory->delete();
            return _res(true, null, 'ลบสำเร็จ', null);
        }else{
            return _res(false, null, 'เกิดข้อผิดพลาด', null);
        }
    }


    public function submitTracking($id = null) {
        if($id){
            $subTrackings = SubTracking::where('subtracking_tracking_id',$id)->get();
            foreach($subTrackings as $subTracking){
                $subTracking->update([
                    'subtracking_status' => 'submit'
                ]);
            }
            $tracking = Tracking::where('id',$id)->first();
            $tracking->update([
                'tracking_status' => 'submit'
            ]);
            if($tracking){
                return _res(true, null, 'บันทึกสำเร็จ', null);
            }else{
                return _res(false, null, 'เกิดข้อผิดพลาด', null);
            }
        }else{
            return _res(false, null, 'เกิดข้อผิดพลาด', null);
        }
    }

    public function submitBooking($id = null) {
        if($id){
            $booking = Booking::where('id',$id)->first(); 
            $trackings = Tracking::where('tracking_booking_id',$booking->id)->get();
            foreach($trackings as $tracking){
                $subTrackings = SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                foreach($subTrackings as $subTracking){
                    $subTracking->update([
                        'subtracking_status' => 'done'
                    ]);
                }
                $tracking->update([
                    'tracking_status' => 'done'
                ]);
            }
            $booking->update([
                'booking_status' => 'done'
            ]);

            if($booking){
                return _res(true, null, 'บันทึกสำเร็จ', null);
            }else{
                return _res(false, null, 'เกิดข้อผิดพลาด', null);
            }
        }else{
            return _res(false, null, 'เกิดข้อผิดพลาด', null);
        }
    }
}
