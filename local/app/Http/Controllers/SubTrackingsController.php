<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Customer;
use App\Model\ParcelType;
use App\Model\ProductPrice;
use App\Model\Tracking;
use App\Model\SubTracking;
use Validator;


class SubTrackingsController extends Controller
{  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subtracking_booking_id' => 'required',
            'subtracking_tracking_id' => 'required',
            'subtracking_dimension_type' => 'required',
            'subtracking_cod' => 'required'
            ]);

        if($validator->fails()) {
            alert()->error('ขออภัย', 'กรุณาเข้าสู่ระบบใหม่ ก่อนเปิดรายการรับพัสดุใหม่')->showConfirmButton("ตกลง","#3085d6");
            return redirect()->back();
        }  

        $subTracking = SubTracking::get();
        $countRow = count($subTracking)+1;
        $subtracking_no = date("Ymd").$countRow;

        $subTracking = SubTracking::create([
            'subtracking_no' => $subtracking_no,
            'subtracking_booking_id' => $request->subtracking_booking_id,
            'subtracking_tracking_id' => $request->subtracking_tracking_id,
            'subtracking_dimension_type' => $request->subtracking_dimension_type,
            'subtracking_cod' => $request->subtracking_cod,
            'subtracking_price' => $subtracking_price,
            'subtracking_status' => "done"

        ]);

        $tracking_id = $request->subtracking_tracking_id;

    }

    public function updateSubTracking(Request $request, $id) {
        // return $request->all();

        $subTracking = SubTracking::where('subtracking_tracking_id',$request->tracking_id)
        ->where('subtracking_price','-')
        ->get();

        if($subTracking) {
            $subTracking->update([
                'subtracking_price' => $request->priceFromDimension
            ]);

            $booking_id = $subTracking->subtracking_booking_id;
            $trackings = Tracking::where('id',$request->tracking_id)->first();
            $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
            $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
            $parcelTypes = ParcelType::get();
            $productPrices = ProductPrice::get();
            return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices']));
        }else{
            alert()->error('ขออภัย', 'ขออภัย')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
}
