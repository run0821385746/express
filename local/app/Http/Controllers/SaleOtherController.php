<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\SaleOther;
use App\Model\Tracking;
use App\Model\Customer;
use App\Model\SubTracking;
use App\Model\ParcelType;
use App\Model\ProductPrice;
use App\Model\Employee;
use App\Model\Booking;
use Auth;
use DataTables;

class SaleOtherController extends Controller
{
    public function deleteProductInList($id = null) {
        if($id){
            $saleOther = SaleOther::find($id);
            $trackings = Tracking::where('id',$saleOther->sale_other_tr_id)->first();
            $booking = Booking::find($trackings->tracking_booking_id);
            $Customer_sender = Customer::find($booking->booking_sender_id);
            $customer = Customer::where('id',$trackings->tracking_receiver_id)->first();
            $subTrackingList = SubTracking::where('subtracking_tracking_id',$trackings->id)->get();
            $parcelTypes= ParcelType::get();
            $productPrices=ProductPrice::get();
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();

            $saleOther->delete();

            $subTrackings = SubTracking::where('subtracking_tracking_id',$saleOther->sale_other_tr_id)->get();
            $amount = 0;
            foreach($subTrackings as $subTracking){
                $amount += $subTracking->subtracking_price;
            }

            $SaleOthers = SaleOther::where('sale_other_tr_id',$saleOther->sale_other_tr_id)->get();
            foreach($SaleOthers as $SaleOtherabject){
                $amount += $SaleOtherabject->sale_other_price;
            }

            $trackings->update([
                'tracking_amount' => $amount
            ]);
            
            $saleOtherList = SaleOther::where('sale_other_tr_id',$trackings->id)->get();
            alert()->success('สำเร็จ', 'ลบรายการสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return view('Receives/receive_add_parcel',compact(['customer','trackings','subTrackingList','parcelTypes','productPrices','employee','saleOtherList','Customer_sender']));
        
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
    public function getSaleOtherList() {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee->id)->first();

        return view('/Receives.receive_sale_other',compact(['employee']));
    }
    public function getSaleOtherListDatatable(Request $request) {
        $saleOtherList = SaleOther::where('sale_other_branch_id',$request->branch_id)->get();
        return Datatables::of($saleOtherList)
        ->addIndexColumn()
        ->editColumn('sale_other_product_id',function($row){
            return $row->productPrice->product_name;
        })
        ->editColumn('sale_other_price',function($row){
            return number_format($row->sale_other_price,2);
        })
        ->make(true);
    }
}
