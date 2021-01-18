<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Booking;
use App\Model\Customer;
use App\Model\Employee;
use App\Model\ParcelType;
use App\Model\ProductPrice;
use App\Model\SubTracking;
use App\Model\Tracking;
use App\Model\Transfer;
use App\Model\PostCode;
use App\Model\SaleOther;
use App\Model\province;
use App\Model\DropCenter;
use App\Model\CourierCall;
use App\Model\TransferDropCenter;
use App\Model\ParcelWrongs;
use DB;
use App\Model\DimensionHistory;
use Validator;
use Auth;
use DataTables;

class TrackingsController extends Controller {
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
         if($id) {
            $selected_data = Tracking::find($id);
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

            // $bookingData = Booking::where('id',$selected_data->tracking_booking_id)->first();
            // $parcelTypes= ParcelType::get();
            // $productPrices=ProductPrice::get();
            // $currierList = Employee::where('emp_position','พนักงานจัดส่งพัสดุ(Courier)')
            // ->where('emp_branch_id','1')            
            // ->get();
            // $user = Auth::user();
            // $employee = Employee::where('id',$user->employee_id)->first();
            alert()->success('สำเร็จ', 'ลบรายการสำเร็จ')->showConfirmButton("ตกลง","#3085d6");
            // return redirect()->back();
            return redirect()->to('/connectBooking/'.$booking->id);
            // return view('/input',compact(['bookingData','customer','trackingList','currierList','employee']));
        }
    }

    public function updateReceivingTracking(Request $request, $customer_id) {
        $validator = Validator::make($request->all(), [
            'tracking_id' => 'required',
            'customer_id' => 'required'
            ]);
           
        if($validator->fails()) {
            alert()->error('ขออภัย', 'ขออภัยข้อมูลไม่ถูกต้อง')->showConfirmButton("ตกลง","#3085d6");
            return redirect()->back();
        }  

        $Customer = Customer::find($request->customer_id);
        $PostCodes = PostCode::where('postcode', $Customer->cust_postcode)->first();
        if(!empty($PostCodes)){
            $tracking = Tracking::find($request->tracking_id);
            // dd($request->tracking_id, $tracking);
            if($tracking) {
                $tracking->update([
                    'tracking_receiver_id' => $request->customer_id
                ]);
                $booking_id = $tracking->tracking_booking_id;
                $booking = Booking::find($booking_id);
                $Customer_sender = Customer::find($booking->booking_sender_id);
                $customer = Customer::where('id',$customer_id)->first();
                $trackings = Tracking::where('id',$request->tracking_id)->first();
                $subTrackingList = SubTracking::where('subtracking_tracking_id',$request->tracking_id)->get();
                $parcelTypes = ParcelType::get();
                $productPrices = ProductPrice::get();
                // $currierList = Employee::where('emp_position', "พนักงานจัดส่งพัสดุ(Courier)")->get();
                $user = Auth::user();
                $employee = Employee::where('id',$user->employee_id)->first();           
                return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','Customer_sender']));
            }else{
                alert()->error('ขออภัย', 'ขออภัย')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->back();
            }
        }else{
            $provinces = province::get(); 
            $search_phone = $Customer->cust_phone;
            $user = Auth::user();
            $customers = Customer::where('cust_phone','like', '%'. $search_phone.'%')->get();
            $employee = Employee::where('id',$user->employee_id)->first();
            $tracking_id = $request->tracking_id;
            alert()->error('ขออภัย', 'ที่อยู่ปลายทางไม่อยู่ในพื้นที่ให้บริการ')->showConfirmButton('ตกลง', '#3085d6');
            return view('Customers.customer_search_receive',compact(['customers','tracking_id','search_phone','employee','provinces']));
        }
    }

    public function getTrackingDetail($id = null) { 
    //   return $id;
        if($id){
            // สร้างทางแยกในการสร้าง  tracking id  ใหม่
            $checkOldTracking = Tracking::where('tracking_booking_id',$id)
            ->where('tracking_receiver_id','-')
            ->get();
            if(count($checkOldTracking)>0){
                if($id){ //ถ้ามี booking id  ส่งมาด้วย
                    $trackingStatusRow = Tracking::where('tracking_booking_id',$id)
                    ->where('tracking_amount',null)
                    ->first();
                    if($trackingStatusRow){  //มี tracking เดิมอยู่
                        $trackings = Tracking::where('id',$trackingStatusRow->id)->first();
                        $booking = Booking::find($id);
                        $Customer_sender = Customer::find($booking->booking_sender_id);
                        $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
                        $parcelTypes = ParcelType::get();
                        $productPrices = ProductPrice::get();
                        $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
                        $user = Auth::user();
                        $employee = Employee::where('id',$user->employee_id)->first();
                        $saleOtherList = SaleOther::where('sale_other_tr_id',$trackings->id)->get();

                        return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','saleOtherList','Customer_sender']));
                    
                    }else{ // ไม่มี  tracking  ค้างแล้ว
                        if($id){

                            return view('Receives/receive_add_parcel');
                        }else{
                            alert()->error('ขออภัย', 'ขออภัย')->showConfirmButton('ตกลง', '#3085d6');
                            return redirect()->back();
                        }
                    }
                }else{ //ถ้าไม่มี  booking id 
                    alert()->error('ขออภัย', 'กรุณาเพิ่มข้อมูลผู้ส่งก่อน')->showConfirmButton('ตกลง', '#3085d6');
                    return redirect()->back();
                }
            }else{
                //return "maimee"; ให้สร้าง tracking id  ใหม่
                $booking_id = $id;
                $booking = Booking::find($id);

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
                $jobs_status = "new";
                $trackings = Tracking::create([
                    'tracking_booking_id' => $booking_id,
                    'tracking_receiver_id' => '-',
                    'tracking_parcel_type' => '0',
                    'tracking_status' => $jobs_status
                ]);

                $Customer_sender = Customer::find($booking->booking_sender_id);
                $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
                $parcelTypes = ParcelType::get();
                $productPrices = ProductPrice::get();
                $user = Auth::user();
                $employee = Employee::where('id',$user->employee_id)->first();
               
                return view('Receives/receive_add_parcel',compact(['trackings','subTrackingList','parcelTypes','productPrices','employee','Customer_sender']));
            }
        }else{
            alert()->error('ขออภัย', 'กรุณาเลือกข้อมูลผู้ส่งก่อน')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }    
    }

    public function updateTrackingDetailList($id = null) {

        if($id){
            $countRow_subTracking = SubTracking::where('subtracking_tracking_id',$id)->get();
            $countRow_saleOther = SaleOther::where('sale_other_tr_id',$id)->get();
            
            $countRow = count($countRow_saleOther) + count($countRow_subTracking);
            if($countRow > 0){
                $subtrackings = SubTracking::where('subtracking_tracking_id',$id)->get();
                $amount = 0;
                foreach ($subtrackings as $subtracking) {
                    $amount += $subtracking->subtracking_price;
                    $amount += $subtracking->subtracking_cod_fee;
                }

                $saleOtherList = SaleOther::where('sale_other_tr_id',$id)->get();
                $saleOtherAmount = 0;
                foreach($saleOtherList as $saleOther) {
                    $saleOtherAmount += $saleOther->sale_other_price;
                }
                $amount_Total = $amount + $saleOtherAmount;
                $tracking = Tracking::find($id);
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
                $bookingData = Booking::where('id',$tracking->tracking_booking_id)->first();
                $trackingList = Tracking::where('tracking_no', '!=', '')->where('tracking_no', 'NOT LIKE', '%Destroy')->where('tracking_booking_id',$tracking->tracking_booking_id)->orwhere('tracking_amount', '>', '0')->where('tracking_no', 'NOT LIKE', '%Destroy')->where('tracking_booking_id',$tracking->tracking_booking_id)->get();
                $customer = Customer::where('id',$bookingData->booking_sender_id)->first();
                $user = Auth::user();
                $employee = Employee::where('id',$user->employee_id)->first();

                return view('/input',compact(['bookingData','customer','trackingList','employee']));
                }

            }else{
                alert()->error('ขออภัย', 'ยังไม่มีรายการพัสดุ กรุณาเพิ่มรายการพัสดุก่อน')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
            }
        }else{
            alert()->error('ขออภัย', 'ไม่พบรายละเอียดพัสดุ กรุณาทำรายการก่อน')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
       
    }

    public function tracking_list($id = null) {
        if($id){
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            // $PostCodes = PostCode::where('drop_center_id', $id)->get();
            // $amphure = DB::table('post_codes')
            // ->select('amphures.name_th', 'amphures.id')
            // ->leftJoin('districts', 'post_codes.postcode', '=', 'districts.zip_code')
            // ->leftJoin('amphures', 'districts.amphure_id', '=', 'amphures.id')
            // ->where('post_codes.drop_center_id', $id)
            // ->groupby('amphures.id')
            // ->get();
            
            // $districs = DB::table('post_codes')
            // ->select('districts.name_th', 'districts.id')
            // ->leftJoin('districts', 'post_codes.postcode', '=', 'districts.zip_code')
            // ->where('post_codes.drop_center_id', $id)
            // ->groupby('districts.id')
            // ->get();

            // $trackingList = DB::table('trackings')
            //                     ->select('trackings.*', 'bookings.booking_no','bookings.booking_type')
            //                     ->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')
            //                     ->where('trackings.tracking_status', 'done')
            //                     ->where('bookings.booking_branch_id', $user->emp_branch_id)
            //                     ->get();
                                // dd($trackingList);
            return view('/Receives.tracking_list', compact(['employee','id']));
        }
    }

    public function tracking_listFilter(Request $request){
        $branchid = $request->id;
        if ($request->ajax()) {
            if($request->ListType == "0"){
                // รายการทั้งหมด
                $sql = "
                SELECT
                    DATEDIFF(NOW(), a.orther_dc_revice_time) AS countdate,
                    DATEDIFF(a.send_pick_time, NOW()) AS countdatepick,
                    a.id as subtracking_cod,
                    b.booking_branch_id, d.drop_center_id,
                    b.booking_type, b.booking_no, b.booking_sender_id, c.cust_name,
                    a.*
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                WHERE
                    b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'done' AND a.tracking_no != '' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoingReturn'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourierReturn'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDoneReturn'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id != '$request->id' AND a.tracking_status = 'ReceiveDoneReturn'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id != '$request->id' AND a.tracking_status = 'transferDoingReturn'
                
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDone' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourierReturn' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReturnBack' AND a.parcel_return_amount != '0'

                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'done' AND a.tracking_no != '' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourierReturn' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReturnBack' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDone' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourierReturn' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReturnBack' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                order by
                    a.created_at Desc
                ";
                $trackingList = DB::select($sql);

                return Datatables::of($trackingList)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row){
                    $DropCenter = DropCenter::find($row->booking_branch_id);
                    $SubTrackings = SubTracking::where('subtracking_tracking_id',$row->subtracking_cod)->get();
                    $parcelamount = count($SubTrackings);

                    $created_at = substr($row->created_at, 8,2).'/';
                    $created_at .= substr($row->created_at, 5,2).'/';
                    $created_at .= substr($row->created_at, 0,4).' ';
                    $created_at .= substr($row->created_at, 11,5);

                    $orther_dc_revice_time = substr($row->orther_dc_revice_time, 8,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 5,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 0,4).' ';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 11,5);
                    if (strpos($row->tracking_status, 'Return') !== false) {
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'(RTN)</a>';
                    }else{
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'</a>';
                    }
                    
                    return $tracking_no;
                })
                ->editColumn('cust_name',function($row){
                    return '<div style="white-space: nowrap; width: 100px; overflow: hidden; text-overflow: clip;">'.$row->cust_name.'</div>';
                })
                ->editColumn('booking_type',function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->booking_type == '1'){
                            return $booking_type = 'พัสดุรับหน้าร้าน';
                        }else{
                            return $booking_type = 'เรียกรถเข้ารับพัสดุ';
                        }
                    }else{
                        return $booking_type = 'พัสดุรับจากสาขาต้นทาง';
                    }
                })
                ->editColumn('tracking_status', function($row) use($branchid){
                    if($row->tracking_send_status == 'postpone'){
                        $picktime = substr($row->send_pick_time, 0,10);
                        $date = date('Y-m-d');
                        
                        $date1 = date_create($picktime);
                        $date2 = date_create($date);
                        $diff = date_diff($date2,$date1);
                        $pickcount = $diff->format("%R%a");
                    }else{
                        $pickcount = '0';
                    }

                    if($row->tracking_send_status == 'postpone' && $pickcount > 0 && $row->tracking_status == 'done' || $row->tracking_send_status == 'postpone' && $pickcount > 0 && $row->tracking_status == 'ReceiveDone'){

                        $date = date_create($row->send_pick_time);
                        $picktime = date_format($date,"d/m/Y H:i");
                        return '<span style="color:blue;">เลื่อนรับพัสดุ : <br>'.$picktime.'</span>';
                        
                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'done' || $row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'ReceiveDone'){
                        $indatemeet = date('Y-m-d');
                        $CourierCalls = CourierCall::where('tracking_id', $row->id)->where('created_at', 'like', $indatemeet.'%')->get();
                        if(count($CourierCalls) > 0){
                            return 'นำส่งไม่สำเร็จ(พัสดุเลื่อนรับวันนี้)';
                        }else{
                            return 'ถึงวันนำส่ง(พัสดุเลื่อนรับ)';
                        }

                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'transferDoing'){

                        return 'ทำจ่ายพัสดุเลื่อนรับ';

                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'TransferToCourier'){

                        return 'นำส่งพัสดุเลื่อนรับ';

                    }else if($row->parcel_return_amount > 0 && $row->tracking_status == 'transferDoing' && $pickcount == 0){

                        return 'ทำจ่ายพัสดุให้ Courier อีกครั้ง';

                    }else if($row->parcel_return_amount > 0 && $row->tracking_status == 'TransferToCourier' && $pickcount == 0){

                        return 'นำส่งพัสดุอีกครั้ง';

                    }else if($row->tracking_status == 'done' && $row->parcel_return_amount > 0 || $row->tracking_status == 'ReceiveDone' && $row->parcel_return_amount > 0){

                        return 'นำส่งไม่สำเร็จ';

                    }else if($row->tracking_status == 'transferDoing' && $row->parcel_return_amount > 0){

                        return 'ทำจ่ายพัสดุให้ Courier';

                    }else if($row->tracking_status == 'TransferToCourier' && $row->parcel_return_amount > 0){

                        return 'นำส่งพัสดุอีกครั้ง';

                    }else if($row->tracking_status == 'TransferToCourier'){

                        return 'นำส่งพัสดุอีกครั้ง';

                    }else if($row->tracking_status == 'ReturnBack'){

                        return '<span style="color:red;">รอส่งกลับต้นทาง</span>';

                    }else if($row->booking_branch_id == $branchid && $row->drop_center_id == $branchid && $row->tracking_status == 'ReceiveDoneReturn'){

                        return '<span style="color:red;">รอจ่ายคืนผู้ส่ง</span>';

                    }else if($row->booking_branch_id == $branchid && $row->drop_center_id != $branchid && $row->tracking_status == 'ReceiveDoneReturn'){
                        
                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->subtracking_cod)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<span style="color:red;">รอจ่ายคืนผู้ส่ง(รับจากปลายทาง)</span>';
                        }else{
                            return '<span style="color:red;">รอจ่ายคืนผู้ส่ง</span>';
                        }

                    }else if($row->countdate > 4){

                        return 'ค้างหลังเกิน 4 วัน';

                    }
                })
                ->editColumn('updated_at',function($row){
                   $date = substr($row->updated_at, 8,2).'/';
                   $date .= substr($row->updated_at, 5,2).'/';
                   $date .= substr($row->updated_at, 0,4).' ';
                   $date .= substr($row->updated_at, 11,5);
                    return $date;
                })
                ->addColumn('sendcount', function($row) {
                    // $Transfers = Transfer::where('transfer_tracking_id', $row->subtracking_cod)->groupBy(DB::raw('Date(created_at)'))->get();
                    $Transfers = Transfer::where('transfer_tracking_id', $row->subtracking_cod)->get();
                    return '<a href="#" onclick="findsendHistory(\''.$row->subtracking_cod.'\')">'.count($Transfers).' ครั้ง</a>';
                })
                ->addColumn('inDcdate', function($row) {
                    if($row->orther_dc_revice_time == NULL){
                        $today = date('Y-m-d');
                        $create = substr($row->created_at, 0, 10);
                        $date1 = date_create($create);
                        $date2 = date_create($today);
                        $diff = date_diff($date1,$date2);
                        $countdateinDC = $diff->format("%a วัน");
                    }else{
                        if($row->countdate > 4){
                            $countdateinDC = '<span style="color:red;">'.$row->countdate.' วัน</span>';
                        }else{
                            $countdateinDC = $row->countdate.' วัน';
                        }
                    }
                    return $countdateinDC;
                })
                ->addColumn('action', function($row) {
                    if($row->tracking_status == 'ReturnBack'){

                        return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';

                    }else if($row->tracking_status == 'ReceiveDoneReturn'){

                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->id)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        }else{
                            $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->id)->where('wrong_status', 'true')->first();
                            if(!empty($ParcelWrongs)){
                                $Transfer = Transfer::where('transfer_tracking_id', $row->id)->where('created_at','>', $ParcelWrongs->created_at)->orderby('id','desc')->first();
                                // dd(!empty($Transfer));
                                if(!empty($Transfer)){
                                    return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');" disabled>ยกเลิกส่งกลับ</button>';
                                }else{
                                    return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';
                                }
                            }else{
                                return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');" disabled>ยกเลิกส่งกลับ</button>';
                            }
                        }

                    }else{
                        if($row->tracking_status == 'done' || $row->tracking_status == 'ReceiveDone'){

                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-danger" onClick="addStatusWrong(\''.$row->subtracking_cod.'\');">แจ้งส่งกลับ</button>';

                        }else{

                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';

                        }
                    }
                })
                ->rawColumns(['action' => 'action','cust_name' => 'cust_name','tracking_no' => 'tracking_no','sendcount' => 'sendcount','tracking_status' => 'tracking_status','inDcdate' => 'inDcdate'])
                ->make(true);
            }else if($request->ListType === "1"){
                $sql = "
                SELECT
                    DATEDIFF(NOW(), a.orther_dc_revice_time) AS countdate,
                    DATEDIFF(a.send_pick_time, NOW()) AS countdatepick,
                    a.id as subtracking_cod,
                    b.booking_branch_id, d.drop_center_id,
                    b.booking_type, b.booking_no, c.cust_name,
                    a.*
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                WHERE
                    b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'done' AND a.parcel_return_amount != '0' AND a.tracking_send_status = 'postpone' AND DATEDIFF(a.send_pick_time, NOW()) >= 0
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND a.parcel_return_amount != '0' AND a.tracking_send_status = 'postpone' AND DATEDIFF(a.send_pick_time, NOW()) >= 0
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND a.parcel_return_amount != '0' AND a.tracking_send_status = 'postpone' AND DATEDIFF(a.send_pick_time, NOW()) >= 0
                
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDone' AND a.parcel_return_amount != '0' AND a.tracking_send_status = 'postpone' AND DATEDIFF(a.send_pick_time, NOW()) >= 0
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND a.parcel_return_amount != '0' AND a.tracking_send_status = 'postpone' AND DATEDIFF(a.send_pick_time, NOW()) >= 0
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND a.parcel_return_amount != '0' AND a.tracking_send_status = 'postpone' AND DATEDIFF(a.send_pick_time, NOW()) >= 0
                order by
                    a.created_at Desc
                ";
                $trackingList = DB::select($sql);

                return Datatables::of($trackingList)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row){
                    $DropCenter = DropCenter::find($row->booking_branch_id);
                    $SubTrackings = SubTracking::where('subtracking_tracking_id',$row->subtracking_cod)->get();
                    $parcelamount = count($SubTrackings);

                    $created_at = substr($row->created_at, 8,2).'/';
                    $created_at .= substr($row->created_at, 5,2).'/';
                    $created_at .= substr($row->created_at, 0,4).' ';
                    $created_at .= substr($row->created_at, 11,5);

                    $orther_dc_revice_time = substr($row->orther_dc_revice_time, 8,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 5,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 0,4).' ';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 11,5);
                    
                    if (strpos($row->tracking_status, 'Return') !== false) {
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'(RTN)</a>';
                    }else{
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'</a>';
                    }
                    return $tracking_no;
                })
                ->editColumn('cust_name',function($row){
                    return '<div style="white-space: nowrap; width: 100px; overflow: hidden; text-overflow: clip;">'.$row->cust_name.'</div>';
                })
                ->editColumn('booking_type',function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->booking_type == '1'){
                            return $booking_type = 'พัสดุรับหน้าร้าน';
                        }else{
                            return $booking_type = 'เรียกรถเข้ารับพัสดุ';
                        }
                    }else{
                        return $booking_type = 'พัสดุรับจากสาขาต้นทาง';
                    }
                })
                ->editColumn('tracking_status', function($row) use($branchid){
                    if($row->tracking_send_status == 'postpone'){
                        $picktime = substr($row->send_pick_time, 0,10);
                        $date = date('Y-m-d');
                        
                        $date1 = date_create($picktime);
                        $date2 = date_create($date);
                        $diff = date_diff($date2,$date1);
                        $pickcount = $diff->format("%R%a");
                    }else{
                        $pickcount = '0';
                    }

                    if($row->tracking_send_status == 'postpone' && $pickcount > 0 && $row->tracking_status == 'done' || $row->tracking_send_status == 'postpone' && $pickcount > 0 && $row->tracking_status == 'ReceiveDone'){

                        $date = date_create($row->send_pick_time);
                        $picktime = date_format($date,"d/m/Y H:i");
                        return '<span style="color:blue;">เลื่อนรับพัสดุ : <br>'.$picktime.'</span>';
                        
                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'done' || $row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'ReceiveDone'){
                        $indatemeet = date('Y-m-d');
                        $CourierCalls = CourierCall::where('tracking_id', $row->id)->where('created_at', 'like', $indatemeet.'%')->get();
                        if(count($CourierCalls) > 0){
                            return 'นำส่งไม่สำเร็จ(พัสดุเลื่อนรับวันนี้)';
                        }else{
                            return 'ถึงวันนำส่ง(พัสดุเลื่อนรับ)';
                        }

                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'transferDoing'){

                        return 'ทำจ่ายพัสดุเลื่อนรับ';

                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'TransferToCourier'){

                        return 'นำส่งพัสดุเลื่อนรับ';

                    }else if($row->parcel_return_amount > 0 && $row->tracking_status == 'transferDoing' && $pickcount == 0){

                        return 'ทำจ่ายพัสดุให้ Courier อีกครั้ง';

                    }else if($row->parcel_return_amount > 0 && $row->tracking_status == 'TransferToCourier' && $pickcount == 0){

                        return 'นำส่งพัสดุอีกครั้ง';

                    }else if($row->tracking_status == 'done' && $row->parcel_return_amount > 0 || $row->tracking_status == 'ReceiveDone' && $row->parcel_return_amount > 0){

                        return 'นำส่งไม่สำเร็จ';

                    }else if($row->tracking_status == 'transferDoing' && $row->parcel_return_amount > 0){

                        return 'ทำจ่ายพัสดุให้ Courier';

                    }else if($row->tracking_status == 'TransferToCourier' && $row->parcel_return_amount > 0){

                        return 'นำส่งพัสดุอีกครั้ง';

                    }else if($row->tracking_status == 'ReturnBack'){

                        return '<span style="color:red;">รอส่งกลับต้นทาง</span>';

                    }else if($row->booking_branch_id == $branchid && $row->drop_center_id == $branchid && $row->tracking_status == 'ReceiveDoneReturn'){

                        return '<span style="color:red;">รอจ่ายคืนผู้ส่ง</span>';

                    }else if($row->booking_branch_id == $branchid && $row->drop_center_id != $branchid && $row->tracking_status == 'ReceiveDoneReturn'){
                        
                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->subtracking_cod)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<span style="color:red;">รอจ่ายคืนผู้ส่ง(รับจากปลายทาง)</span>';
                        }else{
                            return '<span style="color:red;">รอจ่ายคืนผู้ส่ง</span>';
                        }

                    }else if($row->countdate > 4){

                        return 'ค้างหลังเกิน 4 วัน';

                    }
                })
                ->editColumn('updated_at',function($row){
                   $date = substr($row->updated_at, 8,2).'/';
                   $date .= substr($row->updated_at, 5,2).'/';
                   $date .= substr($row->updated_at, 0,4).' ';
                   $date .= substr($row->updated_at, 11,5);
                    return $date;
                })
                ->addColumn('sendcount', function($row) {
                    // $Transfers = Transfer::where('transfer_tracking_id', $row->subtracking_cod)->groupBy(DB::raw('Date(created_at)'))->get();
                    $Transfers = Transfer::where('transfer_tracking_id', $row->subtracking_cod)->get();
                    return '<a href="#" onclick="findsendHistory(\''.$row->subtracking_cod.'\')">'.count($Transfers).' ครั้ง</a>';
                })
                ->addColumn('inDcdate', function($row) {
                    if($row->orther_dc_revice_time == NULL){
                        $today = date('Y-m-d');
                        $create = substr($row->created_at, 0, 10);
                        $date1 = date_create($create);
                        $date2 = date_create($today);
                        $diff = date_diff($date1,$date2);
                        $countdateinDC = $diff->format("%a วัน");
                    }else{
                        if($row->countdate > 4){
                            $countdateinDC = '<span style="color:red;">'.$row->countdate.' วัน</span>';
                        }else{
                            $countdateinDC = $row->countdate.' วัน';
                        }
                    }
                    return $countdateinDC;
                })
                ->addColumn('action', function($row) {
                    if($row->tracking_status == 'ReturnBack'){

                        return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';

                    }else if($row->tracking_status == 'ReceiveDoneReturn'){

                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->id)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        }else{
                            $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->id)->where('wrong_status', 'true')->first();
                            if(!empty($ParcelWrongs)){
                                $Transfer = Transfer::where('transfer_tracking_id', $row->id)->where('created_at','>', $ParcelWrongs->created_at)->orderby('id','desc')->first();
                                // dd(!empty($Transfer));
                                if(!empty($Transfer)){
                                    return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');" disabled>ยกเลิกส่งกลับ</button>';
                                }else{
                                    return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';
                                }
                            }else{
                                return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');" disabled>ยกเลิกส่งกลับ</button>';
                            }
                            // return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';
                        }

                    }else{
                        if($row->tracking_status == 'done' || $row->tracking_status == 'ReceiveDone'){

                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-danger" onClick="addStatusWrong(\''.$row->subtracking_cod.'\');">แจ้งส่งกลับ</button>';

                        }else{

                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';

                        }
                    }
                })
                ->rawColumns(['action' => 'action','cust_name' => 'cust_name','tracking_no' => 'tracking_no','sendcount' => 'sendcount','tracking_status' => 'tracking_status','inDcdate' => 'inDcdate'])
                ->make(true);
            }else if($request->ListType === "2"){
                $sql = "
                SELECT
                    DATEDIFF(NOW(), a.orther_dc_revice_time) AS countdate,
                    DATEDIFF(a.send_pick_time, NOW()) AS countdatepick,
                    a.id as subtracking_cod,
                    b.booking_branch_id, d.drop_center_id,
                    b.booking_type, b.booking_no, b.booking_sender_id, c.cust_name,
                    a.*
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                WHERE
                    b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'done' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDoneReturn'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id != '$request->id' AND a.tracking_status = 'ReceiveDoneReturn'
                
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDone' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND a.parcel_return_amount != '0'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReturnBack' AND a.parcel_return_amount != '0'
                order by
                    a.created_at Desc
                ";
                $trackingList = DB::select($sql);

                return Datatables::of($trackingList)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row){
                    $DropCenter = DropCenter::find($row->booking_branch_id);
                    $SubTrackings = SubTracking::where('subtracking_tracking_id',$row->subtracking_cod)->get();
                    $parcelamount = count($SubTrackings);

                    $created_at = substr($row->created_at, 8,2).'/';
                    $created_at .= substr($row->created_at, 5,2).'/';
                    $created_at .= substr($row->created_at, 0,4).' ';
                    $created_at .= substr($row->created_at, 11,5);

                    $orther_dc_revice_time = substr($row->orther_dc_revice_time, 8,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 5,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 0,4).' ';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 11,5);
                    
                    if (strpos($row->tracking_status, 'Return') !== false) {
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'(RTN)</a>';
                    }else{
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'</a>';
                    }
                    return $tracking_no;
                })
                ->editColumn('cust_name',function($row){
                    return '<div style="white-space: nowrap; width: 100px; overflow: hidden; text-overflow: clip;">'.$row->cust_name.'</div>';
                })
                ->editColumn('booking_type',function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->booking_type == '1'){
                            return $booking_type = 'พัสดุรับหน้าร้าน';
                        }else{
                            return $booking_type = 'เรียกรถเข้ารับพัสดุ';
                        }
                    }else{
                        return $booking_type = 'พัสดุรับจากสาขาต้นทาง';
                    }
                })
                ->editColumn('tracking_status', function($row) use($branchid){
                    if($row->tracking_send_status == 'postpone'){
                        $picktime = substr($row->send_pick_time, 0,10);
                        $date = date('Y-m-d');
                        
                        $date1 = date_create($picktime);
                        $date2 = date_create($date);
                        $diff = date_diff($date2,$date1);
                        $pickcount = $diff->format("%R%a");
                    }else{
                        $pickcount = '0';
                    }

                    if($row->tracking_send_status == 'postpone' && $pickcount > 0 && $row->tracking_status == 'done' || $row->tracking_send_status == 'postpone' && $pickcount > 0 && $row->tracking_status == 'ReceiveDone'){

                        $date = date_create($row->send_pick_time);
                        $picktime = date_format($date,"d/m/Y H:i");
                        return '<span style="color:blue;">เลื่อนรับพัสดุ : <br>'.$picktime.'</span>';
                        
                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'done' || $row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'ReceiveDone'){
                        $indatemeet = date('Y-m-d');
                        $CourierCalls = CourierCall::where('tracking_id', $row->id)->where('created_at', 'like', $indatemeet.'%')->get();
                        if(count($CourierCalls) > 0){
                            return 'นำส่งไม่สำเร็จ(พัสดุเลื่อนรับวันนี้)';
                        }else{
                            return 'ถึงวันนำส่ง(พัสดุเลื่อนรับ)';
                        }

                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'transferDoing'){

                        return 'ทำจ่ายพัสดุเลื่อนรับ';

                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'TransferToCourier'){

                        return 'นำส่งพัสดุเลื่อนรับ';

                    }else if($row->parcel_return_amount > 0 && $row->tracking_status == 'transferDoing' && $pickcount == 0){

                        return 'ทำจ่ายพัสดุให้ Courier อีกครั้ง';

                    }else if($row->parcel_return_amount > 0 && $row->tracking_status == 'TransferToCourier' && $pickcount == 0){

                        return 'นำส่งพัสดุอีกครั้ง';

                    }else if($row->tracking_status == 'TransferToCourier'){

                        return 'นำส่งพัสดุอีกครั้ง';

                    }else if($row->tracking_status == 'done' && $row->parcel_return_amount > 0 || $row->tracking_status == 'ReceiveDone' && $row->parcel_return_amount > 0){

                        return 'นำส่งไม่สำเร็จ';

                    }else if($row->tracking_status == 'transferDoing' && $row->parcel_return_amount > 0){

                        return 'ทำจ่ายพัสดุให้ Courier';

                    }else if($row->tracking_status == 'TransferToCourier' && $row->parcel_return_amount > 0){

                        return 'นำส่งพัสดุอีกครั้ง';

                    }else if($row->tracking_status == 'ReturnBack'){

                        return '<span style="color:red;">รอส่งกลับต้นทาง</span>';

                    }else if($row->booking_branch_id == $branchid && $row->drop_center_id == $branchid && $row->tracking_status == 'ReceiveDoneReturn'){

                        return '<span style="color:red;">รอจ่ายคืนผู้ส่ง</span>';

                    }else if($row->booking_branch_id == $branchid && $row->drop_center_id != $branchid && $row->tracking_status == 'ReceiveDoneReturn'){
                        
                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->subtracking_cod)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<span style="color:red;">รอจ่ายคืนผู้ส่ง(รับจากปลายทาง)</span>';
                        }else{
                            return '<span style="color:red;">รอจ่ายคืนผู้ส่ง</span>';
                        }

                    }else if($row->countdate > 4){

                        return 'ค้างหลังเกิน 4 วัน';

                    }
                })
                ->editColumn('updated_at',function($row){
                   $date = substr($row->updated_at, 8,2).'/';
                   $date .= substr($row->updated_at, 5,2).'/';
                   $date .= substr($row->updated_at, 0,4).' ';
                   $date .= substr($row->updated_at, 11,5);
                    return $date;
                })
                ->addColumn('sendcount', function($row) {
                    // $Transfers = Transfer::where('transfer_tracking_id', $row->subtracking_cod)->groupBy(DB::raw('Date(created_at)'))->get();
                    $Transfers = Transfer::where('transfer_tracking_id', $row->subtracking_cod)->get();
                    return '<a href="#" onclick="findsendHistory(\''.$row->subtracking_cod.'\')">'.count($Transfers).' ครั้ง</a>';
                })
                ->addColumn('inDcdate', function($row) {
                    if($row->orther_dc_revice_time == NULL){
                        $today = date('Y-m-d');
                        $create = substr($row->created_at, 0, 10);
                        $date1 = date_create($create);
                        $date2 = date_create($today);
                        $diff = date_diff($date1,$date2);
                        $countdateinDC = $diff->format("%a วัน");
                    }else{
                        if($row->countdate > 4){
                            $countdateinDC = '<span style="color:red;">'.$row->countdate.' วัน</span>';
                        }else{
                            $countdateinDC = $row->countdate.' วัน';
                        }
                    }
                    return $countdateinDC;
                })
                ->addColumn('action', function($row) {
                    if($row->tracking_status == 'ReturnBack'){

                        return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';

                    }else if($row->tracking_status == 'ReceiveDoneReturn'){

                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->id)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        }else{
                            $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->id)->where('wrong_status', 'true')->first();
                            if(!empty($ParcelWrongs)){
                                $Transfer = Transfer::where('transfer_tracking_id', $row->id)->where('created_at','>', $ParcelWrongs->created_at)->orderby('id','desc')->first();
                                // dd(!empty($Transfer));
                                if(!empty($Transfer)){
                                    return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');" disabled>ยกเลิกส่งกลับ</button>';
                                }else{
                                    return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';
                                }
                            }else{
                                return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');" disabled>ยกเลิกส่งกลับ</button>';
                            }
                            // return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';
                        }

                    }else{
                        if($row->tracking_status == 'done' || $row->tracking_status == 'ReceiveDone'){

                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-danger" onClick="addStatusWrong(\''.$row->subtracking_cod.'\');">แจ้งส่งกลับ</button>';

                        }else{

                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';

                        }
                    }
                })
                ->rawColumns(['action' => 'action','cust_name' => 'cust_name','tracking_no' => 'tracking_no','sendcount' => 'sendcount','tracking_status' => 'tracking_status','inDcdate' => 'inDcdate'])
                ->make(true);
            }else if($request->ListType === "3"){
                $sql = "
                SELECT
                    DATEDIFF(NOW(), a.orther_dc_revice_time) AS countdate,
                    DATEDIFF(a.send_pick_time, NOW()) AS countdatepick,
                    a.id as subtracking_cod,
                    b.booking_branch_id, d.drop_center_id,
                    b.booking_type, b.booking_no, b.booking_sender_id, c.cust_name,
                    a.*
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                WHERE
                    b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'done' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReturnBack' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDoneReturn' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id != '$request->id' AND a.tracking_status = 'ReceiveDoneReturn' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDone' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReturnBack' AND DATEDIFF(NOW(), a.orther_dc_revice_time) > 4
                order by
                    a.created_at Desc
                ";
                $trackingList = DB::select($sql);

                return Datatables::of($trackingList)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row){
                    $DropCenter = DropCenter::find($row->booking_branch_id);
                    $SubTrackings = SubTracking::where('subtracking_tracking_id',$row->subtracking_cod)->get();
                    $parcelamount = count($SubTrackings);

                    $created_at = substr($row->created_at, 8,2).'/';
                    $created_at .= substr($row->created_at, 5,2).'/';
                    $created_at .= substr($row->created_at, 0,4).' ';
                    $created_at .= substr($row->created_at, 11,5);

                    $orther_dc_revice_time = substr($row->orther_dc_revice_time, 8,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 5,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 0,4).' ';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 11,5);
                    
                    if (strpos($row->tracking_status, 'Return') !== false) {
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'(RTN)</a>';
                    }else{
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'</a>';
                    }
                    return $tracking_no;
                })
                ->editColumn('cust_name',function($row){
                    return '<div style="white-space: nowrap; width: 100px; overflow: hidden; text-overflow: clip;">'.$row->cust_name.'</div>';
                })
                ->editColumn('booking_type',function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->booking_type == '1'){
                            return $booking_type = 'พัสดุรับหน้าร้าน';
                        }else{
                            return $booking_type = 'เรียกรถเข้ารับพัสดุ';
                        }
                    }else{
                        return $booking_type = 'พัสดุรับจากสาขาต้นทาง';
                    }
                })
                ->editColumn('tracking_status', function($row) use($branchid){
                    if($row->tracking_send_status == 'postpone'){
                        $picktime = substr($row->send_pick_time, 0,10);
                        $date = date('Y-m-d');
                        
                        $date1 = date_create($picktime);
                        $date2 = date_create($date);
                        $diff = date_diff($date2,$date1);
                        $pickcount = $diff->format("%R%a");
                    }else{
                        $pickcount = '0';
                    }

                    if($row->tracking_send_status == 'postpone' && $pickcount > 0 && $row->tracking_status == 'done' || $row->tracking_send_status == 'postpone' && $pickcount > 0 && $row->tracking_status == 'ReceiveDone'){

                        $date = date_create($row->send_pick_time);
                        $picktime = date_format($date,"d/m/Y H:i");
                        return '<span style="color:blue;">เลื่อนรับพัสดุ : <br>'.$picktime.'</span>';
                        
                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'done' || $row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'ReceiveDone'){
                        $indatemeet = date('Y-m-d');
                        $CourierCalls = CourierCall::where('tracking_id', $row->id)->where('created_at', 'like', $indatemeet.'%')->get();
                        if(count($CourierCalls) > 0){
                            return 'นำส่งไม่สำเร็จ(พัสดุเลื่อนรับวันนี้)';
                        }else{
                            return 'ถึงวันนำส่ง(พัสดุเลื่อนรับ)';
                        }

                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'transferDoing'){

                        return 'ทำจ่ายพัสดุเลื่อนรับ';

                    }else if($row->tracking_send_status == 'postpone' && $pickcount == 0 && $row->tracking_status == 'TransferToCourier'){

                        return 'นำส่งพัสดุเลื่อนรับ';

                    }else if($row->parcel_return_amount > 0 && $row->tracking_status == 'transferDoing' && $pickcount == 0){

                        return 'ทำจ่ายพัสดุให้ Courier อีกครั้ง';

                    }else if($row->parcel_return_amount > 0 && $row->tracking_status == 'TransferToCourier' && $pickcount == 0){

                        return 'นำส่งพัสดุอีกครั้ง';

                    }else if($row->tracking_status == 'done' && $row->parcel_return_amount > 0 || $row->tracking_status == 'ReceiveDone' && $row->parcel_return_amount > 0){

                        return 'นำส่งไม่สำเร็จ';

                    }else if($row->tracking_status == 'transferDoing' && $row->parcel_return_amount > 0){

                        return 'ทำจ่ายพัสดุให้ Courier';

                    }else if($row->tracking_status == 'TransferToCourier' && $row->parcel_return_amount > 0){

                        return 'นำส่งพัสดุอีกครั้ง';

                    }else if($row->tracking_status == 'ReturnBack'){

                        return '<span style="color:red;">รอส่งกลับต้นทาง</span>';

                    }else if($row->booking_branch_id == $branchid && $row->drop_center_id == $branchid && $row->tracking_status == 'ReceiveDoneReturn'){

                        return '<span style="color:red;">รอจ่ายคืนผู้ส่ง</span>';

                    }else if($row->booking_branch_id == $branchid && $row->drop_center_id != $branchid && $row->tracking_status == 'ReceiveDoneReturn'){
                        
                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->subtracking_cod)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<span style="color:red;">รอจ่ายคืนผู้ส่ง(รับจากปลายทาง)</span>';
                        }else{
                            return '<span style="color:red;">รอจ่ายคืนผู้ส่ง</span>';
                        }

                    }else if($row->countdate > 4){

                        return 'ค้างหลังเกิน 4 วัน';

                    }
                })
                ->editColumn('updated_at',function($row){
                   $date = substr($row->updated_at, 8,2).'/';
                   $date .= substr($row->updated_at, 5,2).'/';
                   $date .= substr($row->updated_at, 0,4).' ';
                   $date .= substr($row->updated_at, 11,5);
                    return $date;
                })
                ->addColumn('sendcount', function($row) {
                    // $Transfers = Transfer::where('transfer_tracking_id', $row->subtracking_cod)->groupBy(DB::raw('Date(created_at)'))->get();
                    $Transfers = Transfer::where('transfer_tracking_id', $row->subtracking_cod)->get();
                    return '<a href="#" onclick="findsendHistory(\''.$row->subtracking_cod.'\')">'.count($Transfers).' ครั้ง</a>';
                })
                ->addColumn('inDcdate', function($row) {
                    if($row->orther_dc_revice_time == NULL){
                        $today = date('Y-m-d');
                        $create = substr($row->created_at, 0, 10);
                        $date1 = date_create($create);
                        $date2 = date_create($today);
                        $diff = date_diff($date1,$date2);
                        $countdateinDC = $diff->format("%a วัน");
                    }else{
                        if($row->countdate > 4){
                            $countdateinDC = '<span style="color:red;">'.$row->countdate.' วัน</span>';
                        }else{
                            $countdateinDC = $row->countdate.' วัน';
                        }
                    }
                    return $countdateinDC;
                })
                ->addColumn('action', function($row) {
                    if($row->tracking_status == 'ReturnBack'){

                        return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';

                    }else if($row->tracking_status == 'ReceiveDoneReturn'){

                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->id)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        }else{
                            $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->id)->where('wrong_status', 'true')->first();
                            if(!empty($ParcelWrongs)){
                                $Transfer = Transfer::where('transfer_tracking_id', $row->id)->where('created_at','>', $ParcelWrongs->created_at)->orderby('id','desc')->first();
                                // dd(!empty($Transfer));
                                if(!empty($Transfer)){
                                    return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');" disabled>ยกเลิกส่งกลับ</button>';
                                }else{
                                    return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';
                                }
                            }else{
                                return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');" disabled>ยกเลิกส่งกลับ</button>';
                            }
                            // return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->subtracking_cod.'\');">ยกเลิกส่งกลับ</button>';
                        }

                    }else{
                        if($row->tracking_status == 'done' || $row->tracking_status == 'ReceiveDone'){

                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-danger" onClick="addStatusWrong(\''.$row->subtracking_cod.'\');">แจ้งส่งกลับ</button>';

                        }else{

                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>&nbsp;<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';

                        }
                    }
                })
                ->rawColumns(['action' => 'action','cust_name' => 'cust_name','tracking_no' => 'tracking_no','sendcount' => 'sendcount','tracking_status' => 'tracking_status','inDcdate' => 'inDcdate'])
                ->make(true);
            }
        }
        // dd($request->id);
    }

    public function find_sendHistory(Request $request){
        // dd($request->id);
        $Transfers = Transfer::where('transfer_tracking_id', $request->id)->orderby('created_at','Asc')->get();
        $i = 0;
        $tranfer_detail = '[';
        foreach ($Transfers as $Transfer) {
            $i++;
            if($i == 1){
                $employee = Employee::where('id',$Transfer->transfer_courier_id)->first();
                $CourierCalls = CourierCall::where('tranfer_id', $Transfer->id)->where('tracking_id', $Transfer->transfer_tracking_id)->get();
                $CourierCalls_json = json_encode($CourierCalls, JSON_UNESCAPED_UNICODE);
                $tranfer_detail .= '{
                    "courier":"'.$employee->emp_firstname.' '.$employee->emp_lastname.'",
                    "date":"'.$Transfer->created_at.'",
                    "call":'.$CourierCalls_json.'
                }';
            }else{
                $employee = Employee::where('id',$Transfer->transfer_courier_id)->first();
                $CourierCalls = CourierCall::where('tranfer_id', $Transfer->id)->where('tracking_id', $Transfer->transfer_tracking_id)->get();
                $CourierCalls_json = json_encode($CourierCalls, JSON_UNESCAPED_UNICODE);
                $tranfer_detail .= ',{
                    "courier":"'.$employee->emp_firstname.' '.$employee->emp_lastname.'",
                    "date":"'.$Transfer->created_at.'",
                    "call":'.$CourierCalls_json.'
                }';
            }
        }
        $tranfer_detail .= ']';
        return json_encode($tranfer_detail);
    }

    public function getTrackingDetailFormTrackingId($id = null) {
        if($id) {
            $trackings = Tracking::find($id);
            $booking = Booking::find($trackings->tracking_booking_id);
            $Customer_sender = Customer::find($booking->booking_sender_id);
            $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
            $subTrackingList = SubTracking::where('subtracking_tracking_id',$id)->get();
            $parcelTypes = ParcelType::get();
            $productPrices = ProductPrice::get();
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $saleOtherList = SaleOther::where('sale_other_tr_id',$trackings->id)->get();
           
            return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','saleOtherList','Customer_sender']));
        }else{
            alert()->error('ขออภัย', 'ไม่พบรายละเอียดพัสดุ กรุณาทำรายการก่อน')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
}
