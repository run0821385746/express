<?php

namespace App\Http\Controllers;
use App\Model\Booking;
use App\Model\Employee;
use App\Model\Tracking;
use App\Model\DropCenter;
use App\Model\SubTracking;
use App\Model\Customer;
use DB;
use Auth;
use PDF;
use Dompdf\Dompdf;

use Illuminate\Http\Request;  

class SlipController extends Controller
{
    public function previewSlipReceiveParcel($id = null, $money = null) {
        if($id) {
            $user = Auth::user();
            // dd($money);
            $booking = Booking::find($id);
            if($money == null || $money == 'A4'){
                $recive = $booking->receive_money;
                $slipchange = $booking->receive_money-$booking->booking_amount;
            }else{
                $slipchange = $money-$booking->booking_amount;
                $recive = $money;
            }
            
            // dd($slipchange);
            $customer_sender = Customer::find($booking->booking_sender_id);
            $employee = Employee::find($booking->create_by);
            $dropCenter = DropCenter::where('id',$employee->emp_branch_id)->first();
            $trackings = DB::table('trackings')
                        ->select('trackings.id', 'trackings.tracking_no', 'trackings.tracking_booking_id', 'trackings.tracking_receiver_id', 'trackings.tracking_amount', 'customers.cust_name', 'customers.cust_district', 'customers.cust_postcode')
                        ->leftJoin('customers', 'trackings.tracking_receiver_id', '=', 'customers.id')
                        ->where('trackings.tracking_booking_id', '=', $booking->id)
                        ->where('tracking_no', 'not like', "%Destroy%")
                        ->get();
                        // dd($trackings);
            // $trackings = Tracking::where('tracking_booking_id',$booking->id)->leftJoin('customers', 'users.id', '=', 'customers.user_id')->get();
            $papersize = array(0,0,1000,205);
            $Angle = 'landscape';
            if($money == 'A4'){
                $papersize = 'A4';
                $Angle = 'portrait';
            }
            $pdf = PDF::loadView('/Receives.preview_slip',compact(['booking','trackings','dropCenter','employee','customer_sender','slipchange','recive']))->setPaper($papersize, $Angle);
            return $pdf->stream();
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
    
    public function preview_slipApp($id = null) {
        if($id) {
            $user = Auth::user();
            // dd($money);
            $booking = Booking::find($id);
            // if($money == null || $money == 'A4'){
            //     $recive = $booking->receive_money;
            //     $slipchange = $booking->receive_money-$booking->booking_amount;
            // }else{
            //     $slipchange = $money-$booking->booking_amount;
            //     $recive = $money;
            // }
            $recive = $booking->receive_money;
            $slipchange = $booking->receive_money-$booking->booking_amount;
            
            // dd($slipchange);
            $customer_sender = Customer::find($booking->booking_sender_id);
            $employee = Employee::find($booking->create_by);
            $dropCenter = DropCenter::where('id',$employee->emp_branch_id)->first();
            $trackings = DB::table('trackings')
                        ->select('trackings.id', 'trackings.tracking_no', 'trackings.tracking_booking_id', 'trackings.tracking_receiver_id', 'trackings.tracking_amount', 'customers.cust_name', 'customers.cust_district', 'customers.cust_postcode')
                        ->leftJoin('customers', 'trackings.tracking_receiver_id', '=', 'customers.id')
                        ->where('trackings.tracking_booking_id', '=', $booking->id)
                        ->where('tracking_no', 'not like', "%Destroy%")
                        ->get();
                        // dd($trackings);
            // $trackings = Tracking::where('tracking_booking_id',$booking->id)->leftJoin('customers', 'users.id', '=', 'customers.user_id')->get();
            // $papersize = array(0,0,1000,205);
            // $Angle = 'landscape';
            // if($money == 'A4'){
            //     $papersize = 'A4';
            //     $Angle = 'portrait';
            // }
            $papersize = 'A4';
            $Angle = 'portrait';
            $pdf = PDF::loadView('/Receives.preview_slipApp',compact(['booking','trackings','dropCenter','employee','customer_sender','slipchange','recive']))->setPaper($papersize, $Angle);
            return $pdf->stream();
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
    
    public function previewTrackingBarcode($id = null) {
        if($id) {
            $user = Auth::user();
            $employee = Employee::find($user->employee_id);
            $dropCenter = DropCenter::where('id',$employee->emp_branch_id)->first();
            $tracking = Tracking::find($id);
            $booking = Booking::where('id',$tracking->tracking_booking_id)->first();
            $subTrackings = SubTracking::where('subtracking_tracking_id',$id)->get();
            $papersize = array(0,0,681.6,374.4);
            $pdf = PDF::loadView('/Receives.preview_barcode',compact(['booking','tracking','dropCenter','subTrackings']))->setPaper($papersize, 'landscape');
            return $pdf->stream();
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
    
    public function previewTrackingBarcode_all_booking($id = null) {
        if($id) {
            $user = Auth::user();
            $employee = Employee::find($user->employee_id);
            $dropCenter = DropCenter::where('id',$employee->emp_branch_id)->first();
            $booking = Booking::find($id);
            $trackings = Tracking::where('tracking_booking_id', $booking->id)->get();
            // $subTrackings = SubTracking::where('subtracking_tracking_id',$id)->get();
            $papersize = array(0,0,681.6,374.4);
            $pdf = PDF::loadView('/Receives.previewTrackingBarcode_all_booking',compact(['booking','dropCenter','trackings']))->setPaper($papersize, 'landscape');
            return $pdf->stream();
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
    
    public function previewAppTrackingBarcode_all_booking($id = null, $courier_id = null) {
        if($id) {
            // $user = Auth::user();
            $employee = Employee::find($courier_id);
            $dropCenter = DropCenter::where('id',$employee->emp_branch_id)->first();
            $booking = Booking::find($id);
            $trackings = Tracking::where('tracking_booking_id', $booking->id)->get();
            // $subTrackings = SubTracking::where('subtracking_tracking_id',$id)->get();
            // $papersize = array(0,0,681.6,374.4);
            $pdf = PDF::loadView('/Receives.previewAppTrackingBarcode_all_booking',compact(['booking','dropCenter','trackings','courier_id']))->setPaper('A4', 'portrait');
            return $pdf->stream();
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function previewDailyReport() {
        $user = Auth::user();
        if($user){
            $employee = Employee::find($user->employee_id);
            $dropCenter = DropCenter::find($employee->emp_branch_id);
            $today = date('Y-m-d');
            $bookings = Booking::where('booking_branch_id',$employee->emp_branch_id)->where('created_at','like', '%'.$today.'%')->get();
            $pdf = PDF::loadView('/Receives.dailyReport',compact(['employee','dropCenter','bookings']))->setPaper('A4', 'portrait');
            return $pdf->stream();

        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
}
