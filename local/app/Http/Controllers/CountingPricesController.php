<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Customer;
use App\Model\ParcelPrice;
use App\Model\ParcelType;
use App\Model\ProductPrice;
use App\Model\SubTracking;
use App\Model\Tracking;
use App\Model\Employee;
use App\Model\DimensionHistory;
use App\Model\SaleOther;
use App\Model\Booking;
use App\Http\Resources\SubTrackingResource;
use Auth;
use Carbon\Carbon;


use Validator;

class CountingPricesController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($id) {
            $selected_data = SubTracking::find($id);
            // return $selected_data;
            $trackings = Tracking::where('id',$selected_data->subtracking_tracking_id)->first();
            $booking = Booking::find($trackings->tracking_booking_id);
            $Customer_sender = Customer::find($booking->booking_sender_id);
            $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
            $subTracking = SubTracking::find($id);

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

            
            $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
            $parcelTypes= ParcelType::get();
            $productPrices=ProductPrice::get();
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $saleOtherList = SaleOther::where('sale_other_tr_id',$trackings->id)->get();

            alert()->success('สำเร็จ', 'ลบรายการสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            // return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','saleOtherList','Customer_sender']));
            return redirect()->back();
        }
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function countingPrice(Request $request, $id) {

        $validator = Validator::make($request->all(), [
            'parcelType_id' => 'required',
            'selected_dimension_type' => 'required',
            'subtracking_tracking_id' => 'required',
            'subtracking_no' => 'required',
            'weigth' => 'required'
        ]);
        if($request->subtracking_cod == null){
            $request->subtracking_cod = 0;
        }

        if ($validator->fails()) {
            alert()->error('ขออภัย', 'กรุณกรอกรายละเอียดพัสดุที่ต้องการส่งก่อน')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }

        $parcelPrices = ParcelPrice::where('parcel_total_dimension','!=','COD')->where('parcel_price_status', '1')->get();
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
                    alert()->error('ไม่สำเร็จ', 'ขนาดพัสดุเกิน')->showConfirmButton('ตกลง', '#3085d6');
                }else if($request->weigth > $maxweigth){
                    alert()->error('ไม่สำเร็จ', 'น้ำหนักพัสดุเกิน')->showConfirmButton('ตกลง', '#3085d6');
                }

                $trackings = Tracking::where('id',$request->subtracking_tracking_id)->first();
                $booking = Booking::find($trackings->tracking_booking_id);
                $Customer_sender = Customer::find($booking->booking_sender_id);
                $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
                $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
                $parcelTypes= ParcelType::get();
                $productPrices=ProductPrice::get();
                $user = Auth::user();
                $employee = Employee::where('id',$user->employee_id)->first();
                $saleOtherList = SaleOther::where('sale_other_tr_id',$trackings->id)->get();
                return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','saleOtherList','Customer_sender']));
            }else{
                // ต้องวิ่งเอาdimensionไปเก็บด้วย
                $subTracking = DimensionHistory::create([
                    'dimension_history_tracking_id' => $request->subtracking_tracking_id,
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
                    alert()->error('ไม่สำเร็จ', 'ขนาดพัสดุเกิน')->showConfirmButton('ตกลง', '#3085d6');
                    
                }else if($request->weigth > $maxweigth){
                    alert()->error('ไม่สำเร็จ', 'น้ำหนักพัสดุเกิน')->showConfirmButton('ตกลง', '#3085d6');
                }
                $trackings = Tracking::where('id',$request->subtracking_tracking_id)->first();
                $booking = Booking::find($trackings->tracking_booking_id);
                $Customer_sender = Customer::find($booking->booking_sender_id);
                $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
                $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
                $parcelTypes= ParcelType::get();
                $productPrices=ProductPrice::get();
                $user = Auth::user();
                $employee = Employee::where('id',$user->employee_id)->first();
                $saleOtherList = SaleOther::where('sale_other_tr_id',$trackings->id)->get();
                return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','saleOtherList','Customer_sender']));
            }else{
                // ต้องวิ่งเอาdimensionไปเก็บด้วย
                $dimensionHistory = DimensionHistory::create([
                    'dimension_history_tracking_id' => $request->subtracking_tracking_id,
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
        $parcelPrices = ParcelPrice::where('parcel_total_dimension','!=','COD')->where('parcel_price_status', '1')->get();
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
        $tracking = Tracking::where('id',$request->subtracking_tracking_id)->first();
        $booking_id = $tracking->tracking_booking_id;
        $subTracking = SubTracking::get();
        $countRow = count($subTracking)+1;
        $subtracking_no = date("Ymd").$countRow;
        $CODprice = ParcelPrice::where('parcel_total_dimension','=','COD')->first();
        $subtracking_cod_fee = $request->subtracking_cod*($CODprice->parcel_price/100);
        $subTracking = SubTracking::create([
            'subtracking_no' => $subtracking_no,
            'subtracking_booking_id' => $booking_id,
            'subtracking_tracking_id' => $request->subtracking_tracking_id,
            'subtracking_dimension_type' => $request->selected_dimension_type,
            'subtracking_cod' => $request->subtracking_cod,
            'subtracking_cod_fee' => $subtracking_cod_fee,
            'subtracking_price' => $count_parcel_price,
            'subtracking_status' => "new",
            'subtracking_parcel_type' => $request->parcelType_id
        ]);

        $updateSubtrackingIdToHistoryDimension = DimensionHistory::where('dimension_history_tracking_id',$request->subtracking_tracking_id)
        ->where('dimension_history_subtracking_id', null)
        ->first();

        $updateSubtrackingIdToHistoryDimension->update([
            'dimension_history_subtracking_id' => $subTracking->id
        ]);

        $subTrackings = SubTracking::where('subtracking_tracking_id',$request->subtracking_tracking_id)->get();
        $amount = 0;
        foreach($subTrackings as $subTracking){
            $amount += $subTracking->subtracking_price;
            $amount += $subTracking->subtracking_cod_fee;
        }

        $SaleOthers = SaleOther::where('sale_other_tr_id',$request->subtracking_tracking_id)->get();
        foreach($SaleOthers as $SaleOtherabject){
            $amount += $SaleOtherabject->sale_other_price;
        }

        $trackings = Tracking::where('id',$id)->first();

        if($trackings->tracking_no == ''){
            // $date = date('Y-m-d');
            $all_con = array();
            $track_row = Tracking::where('tracking_no', 'LIKE', 'SEV%')->whereDate('created_at', Carbon::today())->get();
            foreach ($track_row as $key => $track_array) {
                array_push($all_con,$track_array->tracking_no);
            }
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

            if(!in_array($tracking_no, $all_con)){
                $check_have_Trackings = Tracking::where('tracking_no', $tracking_no)->get();
                if(count($check_have_Trackings) > 0){

                    $track_row = Tracking::where('tracking_no', 'LIKE', 'SEV%')->whereDate('created_at', Carbon::today())->get();
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

                    $check_have_Trackings = Tracking::where('tracking_no', $tracking_no)->get();
                    if(count($check_have_Trackings) > 0){
                        $trackings->update([
                            'tracking_amount' => $amount
                        ]);
                        alert()->error('ขออภัย', 'เกิดข้อผิดพลาด ในการรันเลข Con')->showConfirmButton('ตกลง', '#3085d6');
                    }else{
                        $trackings->update([
                            'tracking_no' => $tracking_no,
                            'tracking_amount' => $amount
                        ]);
                    }
                }else{
                    $trackings->update([
                        'tracking_no' => $tracking_no,
                        'tracking_amount' => $amount
                    ]);
                }

            }else{
                $track_row = Tracking::where('tracking_no', 'LIKE', 'SEV%')->whereDate('created_at', Carbon::today())->get();
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

                $check_have_Trackings = Tracking::where('tracking_no', $tracking_no)->get();
                if(count($check_have_Trackings) > 0){
                    $trackings->update([
                        'tracking_amount' => $amount
                    ]);
                    alert()->error('ขออภัย', 'เกิดข้อผิดพลาด ในการรันเลข Con')->showConfirmButton('ตกลง', '#3085d6');
                }else{
                    $trackings->update([
                        'tracking_no' => $tracking_no,
                        'tracking_amount' => $amount
                    ]);
                }
            }
        }else{
            $trackings->update([
                'tracking_amount' => $amount
            ]);
        }
        $booking = Booking::find($tracking->tracking_booking_id);
        $Customer_sender = Customer::find($booking->booking_sender_id);
        $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
        $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
        $parcelTypes= ParcelType::get();
        $productPrices=ProductPrice::get();
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $saleOtherList = SaleOther::where('sale_other_tr_id',$trackings->id)->get();
        // return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','saleOtherList','Customer_sender']));
        return redirect()->to('/getTrackingDetailFormTrackingId/'.$trackings->id);
    }

    public function addProductToOrderList($id, $product_id) {
        // return $request->all();
        // $validator = Validator::make($request->all(), [
        //     'product_id' => 'required',
        //     'tracking_id' => 'required'
        // ]);
        // if ($validator->fails()) {
        //     alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
        //     return redirect()->back();
        // }

        $productPrice = ProductPrice::where('id',$product_id)->first();
        // return $productPrice;
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $tracking = Tracking::where('id',$id)->first();
       
        $saleOther = SaleOther::create([
            'sale_other_product_id' => $productPrice->id,
            'sale_other_price' => $productPrice->product_price,
            'sale_other_branch_id' => $user->emp_branch_id,
            'sale_other_booking_id' => $tracking->tracking_booking_id,
            'sale_other_tr_id' => $tracking->id,
        ]);

        if($saleOther){
            $subTrackings = SubTracking::where('subtracking_tracking_id',$id)->get();
            $amount = 0;
            foreach($subTrackings as $subTracking){
                $amount += $subTracking->subtracking_price;
                $amount += $subTracking->subtracking_cod_fee;
            }

            $SaleOthers = SaleOther::where('sale_other_tr_id',$id)->get();
            foreach($SaleOthers as $SaleOtherabject){
                $amount += $SaleOtherabject->sale_other_price;
            }
            // return $amount;

            $tracking->update([
                'tracking_amount' => $amount
            ]);
            // return $tracking->tracking_amount;

            $trackings = Tracking::where('id',$id)->first();
            $booking = Booking::find($tracking->tracking_booking_id);
            $Customer_sender = Customer::find($booking->booking_sender_id);
            $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
            $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
            $parcelTypes= ParcelType::get();
            $productPrices=ProductPrice::get();
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $saleOtherList = SaleOther::where('sale_other_tr_id',$id)->get();

            alert()->success('สำเร็จ', 'เพิ่มรายการสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','saleOtherList','Customer_sender']));
        
        }else{
            alert()->error('ขออภัย', 'เกิดข้อผิดพลาด')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    } 
}
