<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\DropCenter;
use App\Model\Employee;
use App\Model\Tracking;
use App\Model\Transfer;
use App\Model\TranserBill;
use App\Model\SubTracking;
use App\Model\TransferDropCenter;
use App\Model\TransferDropCenterBill;
use App\Model\TrackingsLog;
use App\Model\Customer;
use App\Model\PostCode;
use App\Model\CourierArea;
use App\Model\TranferDropCenterDuplicate;
use App\Model\ReciveTranferDropCenterDuplicate;
use App\Model\TransfersDuplicate;
use App\Model\Booking;
use App\Model\CourierCall;
use App\Model\ParcelWrongs;
use App\Model\ReturnParcel;
use App\Model\PacelCare;
use App\Model\SaleOther;
use App\Model\OrtherSaleView;
use DB;
use Validator;
use PDF;
use Auth;
use DataTables;


class ReportController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


     //ใช้ตอนเลือกผู้ส่งพัสดุแล้วส่งค่ากลับมาหน้า input  เพื่อเอาข้อมูลผู้ส่งมาแสดงใน view
    public function report_form($report_type = null){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $DropCenters = DropCenter::get();
        // dd($DropCenters);
        return view('Roport.Roport',compact(['employee','DropCenters','report_type']));
    }
   
    public function print_report(Request $request){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $validator = Validator::make($request->all(), [
            'dropcenter_pdf' => 'required',
            'report_type_pdf' => 'required',
            'selectdateFrom_pdf' => 'required',
            'selectdateTo_pdf' => 'required'
        ]);
        if($validator->fails()) {
            alert()->error('ขออภัย', 'โปรดรีเฟรชหน้าและลองใหม่อีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
        $dropcenter = $request->dropcenter_pdf;
        $selectdateFrom = $request->selectdateFrom_pdf;
        $selectdateTo = $request->selectdateTo_pdf;
        if($request->dropcenter_pdf == '0'){
            if($request->report_type_pdf == '1'){
                $Tracking = Tracking::where('tracking_no','not like','%Destroy%')->where('tracking_no','!=','')->where('tracking_status','!=','new')->whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.recive_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '2'){
                $Tracking = Transfer::whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->get();
                // dd($Tracking);
                $pdf = PDF::loadView('/Roport.dvl_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '3'){
                $Tracking = Transfer::where('transfer_status', 'CustomerResiveDoneReturn')->whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->orwhere('transfer_status', 'CustomerResiveDone')->whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.dvl_success_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '4'){
                $Tracking = Transfer::where('transfer_status', 'ReturnBackToDC')->whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.dvl_failed_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '5'){
                $Tracking = TransferDropCenter::whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.lh_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '6'){
                $Tracking = Transfer::where('transfer_status', 'CustomerResiveDoneReturn')->whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->orwhere('cod_amount', '>', '0')->where('transfer_status', 'CustomerResiveDone')->whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.cod_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '7'){
                $SaleOthers = OrtherSaleView::where('booking_status', 'done')->whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.orthersale_Report',compact(['SaleOthers','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }
        }else{
            if($request->report_type_pdf == '1'){
                $Tracking = Tracking::select('trackings.*')->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')->where('trackings.tracking_status','!=','new')->where('booking_branch_id', $request->dropcenter_pdf)->where('trackings.tracking_no','not like','%Destroy%')->where('trackings.tracking_no','!=','')->whereDate('trackings.created_at','>=', $request->selectdateFrom_pdf)->whereDate('trackings.created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.recive_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '2'){
                $Tracking = Transfer::select('transfers.*')->leftJoin('trackings', 'transfers.transfer_tracking_id', '=', 'trackings.id')->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')->where('transfers.transfer_branch_id', $request->dropcenter_pdf)->whereDate('transfers.created_at','>=', $request->selectdateFrom_pdf)->whereDate('transfers.created_at','<=', $request->selectdateTo_pdf)->get();
                // dd($Tracking);
                $pdf = PDF::loadView('/Roport.dvl_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '3'){
                $Tracking = Transfer::select('transfers.*')->leftJoin('trackings', 'transfers.transfer_tracking_id', '=', 'trackings.id')->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')->where('transfers.transfer_branch_id', $request->dropcenter_pdf)->where('transfers.transfer_status', 'CustomerResiveDoneReturn')->whereDate('transfers.created_at','>=', $request->selectdateFrom_pdf)->whereDate('transfers.created_at','<=', $request->selectdateTo_pdf)->orwhere('transfers.transfer_status', 'CustomerResiveDone')->whereDate('transfers.created_at','>=', $request->selectdateFrom_pdf)->whereDate('transfers.created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.dvl_success_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '4'){
                $Tracking = Transfer::select('transfers.*')->leftJoin('trackings', 'transfers.transfer_tracking_id', '=', 'trackings.id')->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')->where('transfers.transfer_branch_id', $request->dropcenter_pdf)->where('transfers.transfer_status', 'ReturnBackToDC')->whereDate('transfers.created_at','>=', $request->selectdateFrom_pdf)->whereDate('transfers.created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.dvl_failed_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '5'){
                $Tracking = TransferDropCenter::where('transfer_dropcenter_sender_id', $request->dropcenter_pdf)->whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.lh_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '6'){
                $Tracking = Transfer::select('transfers.*')->leftJoin('trackings', 'transfers.transfer_tracking_id', '=', 'trackings.id')->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')->where('transfers.transfer_branch_id', $request->dropcenter_pdf)->where('transfers.transfer_status', 'CustomerResiveDoneReturn')->whereDate('transfers.created_at','>=', $request->selectdateFrom_pdf)->whereDate('transfers.created_at','<=', $request->selectdateTo_pdf)->orwhere('transfers.cod_amount', '>', '0')->where('transfers.transfer_status', 'CustomerResiveDone')->whereDate('transfers.created_at','>=', $request->selectdateFrom_pdf)->whereDate('transfers.created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.cod_Report',compact(['Tracking','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }else if($request->report_type_pdf == '7'){
                $SaleOthers = OrtherSaleView::select('orther_sale_views.*')->where('sale_other_branch_id', $request->dropcenter_pdf)->where('booking_status', 'done')->whereDate('created_at','>=', $request->selectdateFrom_pdf)->whereDate('created_at','<=', $request->selectdateTo_pdf)->get();
                $pdf = PDF::loadView('/Roport.orthersale_Report',compact(['SaleOthers','employee','dropcenter','selectdateFrom','selectdateTo']));
                return $pdf->stream();
            }
        }
    }
   
    public function report_request(Request $request){
        $validator = Validator::make($request->all(), [
            'dropcenter' => 'required',
            'report_type' => 'required',
            'selectdateFrom' => 'required',
            'selectdateTo' => 'required'
        ]);
        if($validator->fails()) {
            $Trackings = Tracking::where('id', 0)->select();
            return Datatables::of($Trackings)
            ->addIndexColumn()
            ->editColumn('booking_no',function($row){
                return ' ';
            })
            ->editColumn('tracking_no',function($row){
                return ' ';
            })
            ->editColumn('parcel_amount',function($row){
                return ' ';
            })
            ->editColumn('shipping_fee',function($row){
                return ' ';
            })
            ->editColumn('cod_amount',function($row){
                return ' ';
            })
            ->addColumn('action', function($row) {
                return ' ';
            })
            ->rawColumns(['action' => 'action','emp_status' => 'emp_status'])
            ->make(true);
        }  
        if($request->dropcenter == '0'){
            if($request->report_type == '1'){
                
                $Tracking = Tracking::where('tracking_no','not like','%Destroy%')->where('tracking_no','!=','')->where('tracking_status','!=','new')->whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    return $row->tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->id)->get();
                    return count($SubTrackings);
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking_amount,2);
                })
                ->editColumn('cod_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->id)->get();
                    $COD = 0;
                    foreach ($SubTrackings as $SubTracking) {
                        $COD += $SubTracking->subtracking_cod;
                    }
                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    return 'รับเข้าโดย : '.$row->booking->DropCenter->drop_center_name_initial;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);

            }else if($request->report_type == '2'){

                $Tracking = Transfer::whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            $tracking_no = $row->tracking->tracking_no.'(RTN)';
                        }else{
                            $tracking_no = $row->tracking->tracking_no;
                        }
                    }else{
                        $tracking_no = $row->tracking->tracking_no;
                    }
                    return $tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    return count($SubTrackings);
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking->tracking_amount,2);
                })
                ->editColumn('cod_amount', function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    $COD = 0;
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            foreach ($SubTrackings as $SubTracking) {
                                    $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                            }
                        }
                    }else{
                        foreach ($SubTrackings as $SubTracking) {
                            $COD += $SubTracking->subtracking_cod;
                        }
                    }

                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    if(strpos($row->transfer_status, 'TransferToCourier') !== false){
                        $status = "ระหว่างนำส่ง";
                    }else if(strpos($row->transfer_status, 'CustomerResiveDone') !== false){
                        $status = "นำส่งสำเร็จ";
                    }else if(strpos($row->transfer_status, 'ReturnBackToDC') !== false){
                        $status = "นำส่ง<span style='color:red;'>ไม่</span>สำเร็จ";
                    }else{
                        $status = $row->transfer_status;
                    }
                    return $status.' : '.$row->DropCenter->drop_center_name_initial;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);

            }else if($request->report_type == '3'){

                $Tracking = Transfer::where('transfer_status', 'CustomerResiveDoneReturn')->whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->orwhere('transfer_status', 'CustomerResiveDone')->whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            $tracking_no = $row->tracking->tracking_no.'(RTN)';
                        }else{
                            $tracking_no = $row->tracking->tracking_no;
                        }
                    }else{
                        $tracking_no = $row->tracking->tracking_no;
                    }
                    return $tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    return count($SubTrackings);
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking->tracking_amount,2);

                })
                ->editColumn('cod_amount', function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    $COD = 0;
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            foreach ($SubTrackings as $SubTracking) {
                                    $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                            }
                        }
                    }else{
                        foreach ($SubTrackings as $SubTracking) {
                            $COD += $SubTracking->subtracking_cod;
                        }
                    }

                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    return 'นำส่งโดย : '.$row->DropCenter->drop_center_name_initial;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);

            }else if($request->report_type == '4'){

                $Tracking = Transfer::where('transfer_status', 'ReturnBackToDC')->whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            $tracking_no = $row->tracking->tracking_no.'(RTN)';
                        }else{
                            $tracking_no = $row->tracking->tracking_no;
                        }
                    }else{
                        $tracking_no = $row->tracking->tracking_no;
                    }
                    return $tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    return count($SubTrackings);
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking->tracking_amount,2);
                })
                ->editColumn('cod_amount', function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    $COD = 0;
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            foreach ($SubTrackings as $SubTracking) {
                                    $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                            }
                        }
                    }else{
                        foreach ($SubTrackings as $SubTracking) {
                            $COD += $SubTracking->subtracking_cod;
                        }
                    }

                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    return 'นำส่งโดย : '.$row->DropCenter->drop_center_name_initial;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);
                
            }else if($request->report_type == '5'){

                $Tracking = TransferDropCenter::whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->tracking->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_dropcenter_tracking_id)->where('wrong_status', 'true')->first();
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            $tracking_no = $row->tracking->tracking_no.'(RTN)';
                        }else{
                            $tracking_no = $row->tracking->tracking_no;
                        }
                    }else{
                        $tracking_no = $row->tracking->tracking_no;
                    }
                    return $tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    return $row->parcel_amount;
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking->tracking_amount,2);

                })
                ->editColumn('cod_amount', function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_dropcenter_tracking_id)->where('wrong_status', 'true')->first();
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_dropcenter_tracking_id)->get();
                    $COD = 0;
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            foreach ($SubTrackings as $SubTracking) {
                                    $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                            }
                        }
                    }else{
                        foreach ($SubTrackings as $SubTracking) {
                            $COD += $SubTracking->subtracking_cod;
                        }
                    }

                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    return $row->dc_sender->drop_center_name_initial.' - '.$row->dc_receiver->drop_center_name_initial;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);
                
            }else if($request->report_type == '6'){

                $Tracking = Transfer::where('transfer_status', 'CustomerResiveDoneReturn')->whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->orwhere('cod_amount', '>', '0')->where('transfer_status', 'CustomerResiveDone')->whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            $tracking_no = $row->tracking->tracking_no.'(RTN)';
                        }else{
                            $tracking_no = $row->tracking->tracking_no;
                        }
                    }else{
                        $tracking_no = $row->tracking->tracking_no;
                    }
                    return $tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    return count($SubTrackings);
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking->tracking_amount,2);
                })
                ->editColumn('cod_amount', function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    $COD = 0;
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            foreach ($SubTrackings as $SubTracking) {
                                    $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                            }
                        }
                    }else{
                        foreach ($SubTrackings as $SubTracking) {
                            $COD += $SubTracking->subtracking_cod;
                        }
                    }

                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    return 'นำส่งโดย : '.$row->DropCenter->drop_center_name_initial;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);
                
            }else if($request->report_type == '7'){
                $SaleOthers = OrtherSaleView::where('booking_status', 'done')->whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($SaleOthers)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('product_name',function($row){
                    return $row->productPrice->product_name;
                })
                ->editColumn('product_price', function($row){
                    return $row->sale_other_price;
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    return 'ขายโดย : '.$row->DropCenter->drop_center_name_initial;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);

            }else if($request->report_type == '8'){
                $user = Auth::user();
                $employee = Employee::where('id',$user->employee_id)->first();
                $TranserBills = TranserBill::whereDate('created_at','>=', $request->selectdateFrom)
                                            ->whereDate('created_at','<=', $request->selectdateTo)
                                            ->orderby('transfer_bill_status','Desc')
                                            ->get();
                return Datatables::of($TranserBills)
                ->addIndexColumn()
                ->editColumn('transfer_bill_courier_id',function($row){
                    return $row->courier->emp_firstname.' '.$row->courier->emp_lastname;
                })
                ->editColumn('tranfer_by_employee_id',function($row){
                    return $row->employee_creeate->emp_firstname.' '.$row->employee_creeate->emp_lastname;
                })
                ->editColumn('courierPhone', function($row){
                    return $row->courier->emp_phone;
                })
                ->editColumn('transfer_bill_status', function($row){
                    if($row->transfer_bill_status == 'TransferToCourier' || $row->transfer_bill_status == 'sendingCOD'){
                        $Transfers = Transfer::where('transfer_bill_id',$row->id)->get();
                        $btnstatus = 1;
                        foreach ($Transfers as $Transfer) {
                            if($Transfer->transfer_status == 'TransferToCourier'){
                                $btnstatus = 0;
                            }
                        }
                        if($btnstatus == 1){
                            $status = "ดำเนินการสำเร็จ";
                        }else{
                            $status = "ดำเนินการจัดส่ง";
                        }
                    }else if($row->transfer_bill_status == 'done'){
                        $status = "ปิดบิลสำเร็จ";
                    }
                    return $status;
                })
                ->editColumn('CODamount', function($row){
                    $Transfers = Transfer::where('transfer_bill_id',$row->id)->where('transfer_status', 'CustomerResiveDone')->orwhere('transfer_bill_id',$row->id)->where('transfer_status', 'CustomerResiveDoneReturn')->get();
                    $COD = 0;
                    foreach ($Transfers as $Transfer) {
                        if(strpos($Transfer->transfer_status, 'Return') !== false){
                            $SubTrackings = SubTracking::where('subtracking_tracking_id', $Transfer->transfer_tracking_id)->get();
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            $COD += $Transfer->cod_amount;
                        }
                    }
                    return number_format($COD,2);
                })
                ->editColumn('created_at', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) use($employee){
                    $btn = '<a href="/printDeleveryReport/'.$row->id.'" class="btn btn-warning btn-sm" target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);
            }else if($request->report_type == '9'){
                $user = Auth::user();
                $employee = Employee::where('id',$user->employee_id)->first();
                $TransferDropCenterBills = TransferDropCenterBill::whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->orderby('created_at', 'DESC')->get();
                return Datatables::of($TransferDropCenterBills)
                ->addIndexColumn()
                ->editColumn('created_at',function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->editColumn('tranfer_employee_sender_id',function($row){
                    if($row->transfer_bill_status == "receive-done"){
                        return $row->Employee_sender->emp_firstname.'-'.$row->Employee_driver->emp_firstname.'-'.$row->Employee->emp_firstname;
                    }else{
                        return $row->Employee_sender->emp_firstname.'-'.$row->Employee_driver->emp_firstname.'-(ยังไม่ได้รับพัสดุ)';
                    }
                })
                ->editColumn('note', function($row){
                    return $row->dc_sender->drop_center_name_initial.' - '.$row->dc_receiver->drop_center_name_initial;
                })
                ->addColumn('action', function($row) use($employee){
                    $btn = '<a href="/linehallDetail/'.$row->id.'" target="_blank" class="btn btn-warning btn-sm" target="blank"><i class="fa fa-print" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);
            }
        }else{

            // ถ้าอยู่ในสาขา หรือมีการเลือกสาขา
            if($request->report_type == '1'){
                $Tracking = Tracking::select('trackings.*')->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')->where('booking_branch_id', $request->dropcenter)->where('trackings.tracking_no','not like','%Destroy%')->where('trackings.tracking_status','!=','new')->where('trackings.tracking_no','!=','')->whereDate('trackings.created_at','>=', $request->selectdateFrom)->whereDate('trackings.created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    return $row->tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->id)->get();
                    return count($SubTrackings);
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking_amount,2);
                })
                ->editColumn('cod_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->id)->get();
                    $COD = 0;
                    foreach ($SubTrackings as $SubTracking) {
                        $COD += $SubTracking->subtracking_cod;
                    }
                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    // return $row->id;
                    return 'รับเข้าโดย : '.$row->booking->Employee->emp_firstname.' '.$row->booking->Employee->emp_lastname;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);

            }else if($request->report_type == '2'){

                $Tracking = Transfer::select('transfers.*')->leftJoin('trackings', 'transfers.transfer_tracking_id', '=', 'trackings.id')->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')->where('transfers.transfer_branch_id', $request->dropcenter)->whereDate('transfers.created_at','>=', $request->selectdateFrom)->whereDate('transfers.created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            $tracking_no = $row->tracking->tracking_no.'(RTN)';
                        }else{
                            $tracking_no = $row->tracking->tracking_no;
                        }
                    }else{
                        $tracking_no = $row->tracking->tracking_no;
                    }
                    return $tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    return count($SubTrackings);
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking->tracking_amount,2);

                })
                ->editColumn('cod_amount', function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    $COD = 0;
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            foreach ($SubTrackings as $SubTracking) {
                                    $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                            }
                        }
                    }else{
                        foreach ($SubTrackings as $SubTracking) {
                            $COD += $SubTracking->subtracking_cod;
                        }
                    }

                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    if(strpos($row->transfer_status, 'TransferToCourier') !== false){
                        $status = "ระหว่างนำส่ง";
                    }else if(strpos($row->transfer_status, 'CustomerResiveDone') !== false){
                        $status = "นำส่งสำเร็จ";
                    }else if(strpos($row->transfer_status, 'ReturnBackToDC') !== false){
                        $status = "นำส่ง<span style='color:red;'>ไม่</span>สำเร็จ";
                    }else{
                        $status = $row->transfer_status;
                    }
                    return $status.' : '.$row->employee->emp_firstname;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);

            }else if($request->report_type == '3'){

                $Tracking = Transfer::select('transfers.*')->leftJoin('trackings', 'transfers.transfer_tracking_id', '=', 'trackings.id')->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')->where('transfers.transfer_branch_id', $request->dropcenter)->where('transfers.transfer_status', 'CustomerResiveDoneReturn')->whereDate('transfers.created_at','>=', $request->selectdateFrom)->whereDate('transfers.created_at','<=', $request->selectdateTo)->orwhere('transfers.transfer_status', 'CustomerResiveDone')->whereDate('transfers.created_at','>=', $request->selectdateFrom)->whereDate('transfers.created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            $tracking_no = $row->tracking->tracking_no.'(RTN)';
                        }else{
                            $tracking_no = $row->tracking->tracking_no;
                        }
                    }else{
                        $tracking_no = $row->tracking->tracking_no;
                    }
                    return $tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    return count($SubTrackings);
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking->tracking_amount,2);
                })
                ->editColumn('cod_amount', function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    $COD = 0;
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            foreach ($SubTrackings as $SubTracking) {
                                    $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                            }
                        }
                    }else{
                        foreach ($SubTrackings as $SubTracking) {
                            $COD += $SubTracking->subtracking_cod;
                        }
                    }

                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    return 'นำส่งโดย : '.$row->employee->emp_firstname.' '.$row->employee->emp_lastname;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);

            }else if($request->report_type == '4'){

                $Tracking = Transfer::select('transfers.*')->leftJoin('trackings', 'transfers.transfer_tracking_id', '=', 'trackings.id')->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')->where('transfers.transfer_branch_id', $request->dropcenter)->where('transfers.transfer_status', 'ReturnBackToDC')->whereDate('transfers.created_at','>=', $request->selectdateFrom)->whereDate('transfers.created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            $tracking_no = $row->tracking->tracking_no.'(RTN)';
                        }else{
                            $tracking_no = $row->tracking->tracking_no;
                        }
                    }else{
                        $tracking_no = $row->tracking->tracking_no;
                    }
                    return $tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    return count($SubTrackings);
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking->tracking_amount,2);

                })
                ->editColumn('cod_amount', function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    $COD = 0;
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            foreach ($SubTrackings as $SubTracking) {
                                    $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                            }
                        }
                    }else{
                        foreach ($SubTrackings as $SubTracking) {
                            $COD += $SubTracking->subtracking_cod;
                        }
                    }

                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    return 'นำส่งโดย : '.$row->employee->emp_firstname.' '.$row->employee->emp_lastname;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);
                
            }else if($request->report_type == '5'){

                $Tracking = TransferDropCenter::where('transfer_dropcenter_sender_id', $request->dropcenter)->whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->tracking->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_dropcenter_tracking_id)->where('wrong_status', 'true')->first();
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            $tracking_no = $row->tracking->tracking_no.'(RTN)';
                        }else{
                            $tracking_no = $row->tracking->tracking_no;
                        }
                    }else{
                        $tracking_no = $row->tracking->tracking_no;
                    }
                    return $tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    return $row->parcel_amount;
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking->tracking_amount,2);

                })
                ->editColumn('cod_amount', function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_dropcenter_tracking_id)->where('wrong_status', 'true')->first();
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_dropcenter_tracking_id)->get();
                    $COD = 0;
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            foreach ($SubTrackings as $SubTracking) {
                                    $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                            }
                        }
                    }else{
                        foreach ($SubTrackings as $SubTracking) {
                            $COD += $SubTracking->subtracking_cod;
                        }
                    }

                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {

                    return $row->dc_sender->drop_center_name_initial.' - '.$row->dc_receiver->drop_center_name_initial.'<br>'.$row->TransferDropCenterBill->Employee_sender->emp_firstname.' - '.$row->TransferDropCenterBill->Employee_driver->emp_firstname.' - '.$row->TransferDropCenterBill->Employee->emp_firstname;

                })
                ->rawColumns(['action' => 'action'])
                ->make(true);
                
            }else if($request->report_type == '6'){

                $Tracking = Transfer::select('transfers.*')->leftJoin('trackings', 'transfers.transfer_tracking_id', '=', 'trackings.id')->leftJoin('bookings', 'trackings.tracking_booking_id', '=', 'bookings.id')->where('transfers.transfer_branch_id', $request->dropcenter)->where('transfers.transfer_status', 'CustomerResiveDoneReturn')->whereDate('transfers.created_at','>=', $request->selectdateFrom)->whereDate('transfers.created_at','<=', $request->selectdateTo)->orwhere('transfers.cod_amount', '>', '0')->where('transfers.transfer_status', 'CustomerResiveDone')->whereDate('transfers.created_at','>=', $request->selectdateFrom)->whereDate('transfers.created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($Tracking)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('tracking_no',function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            $tracking_no = $row->tracking->tracking_no.'(RTN)';
                        }else{
                            $tracking_no = $row->tracking->tracking_no;
                        }
                    }else{
                        $tracking_no = $row->tracking->tracking_no;
                    }
                    return $tracking_no;
                })
                ->editColumn('parcel_amount', function($row){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    return count($SubTrackings);
                })
                ->editColumn('shipping_fee', function($row){
                    return number_format($row->tracking->tracking_amount,2);

                })
                ->editColumn('cod_amount', function($row){
                    $ParcelWrongs = ParcelWrongs::where('wrong_tracking_id', $row->transfer_tracking_id)->where('wrong_status', 'true')->first();
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->transfer_tracking_id)->get();
                    $COD = 0;
                    if(!empty($ParcelWrongs)){
                        if($row->created_at > $ParcelWrongs->created_at){
                            foreach ($SubTrackings as $SubTracking) {
                                    $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                            }
                        }
                    }else{
                        foreach ($SubTrackings as $SubTracking) {
                            $COD += $SubTracking->subtracking_cod;
                        }
                    }

                    return number_format($COD,2);
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    return 'นำส่งโดย : '.$row->DropCenter->drop_center_name_initial;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);
                
            }else if($request->report_type == '7'){
                $SaleOthers = OrtherSaleView::select('orther_sale_views.*')->where('sale_other_branch_id', $request->dropcenter)->where('booking_status', 'done')->whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->get();
                return Datatables::of($SaleOthers)
                ->addIndexColumn()
                ->editColumn('booking_no',function($row){
                    return $row->booking->booking_no;
                })
                ->editColumn('product_name',function($row){
                    return $row->productPrice->product_name;
                })
                ->editColumn('product_price', function($row){
                    return $row->sale_other_price;
                })
                ->editColumn('datetime', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) {
                    return 'ขายโดย : '.$row->Booking->Employee->emp_firstname.' '.$row->Booking->Employee->emp_lastname;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);

            }else if($request->report_type == '8'){
                $user = Auth::user();
                $employee = Employee::where('id',$user->employee_id)->first();
                $TranserBills = TranserBill::where('tranfer_bill_branch_id',$request->dropcenter)
                                            ->whereDate('created_at','>=', $request->selectdateFrom)
                                            ->whereDate('created_at','<=', $request->selectdateTo)
                                            ->orderby('transfer_bill_status','Desc')
                                            ->get();
                                            // dd($TranserBills);
                return Datatables::of($TranserBills)
                ->addIndexColumn()
                ->editColumn('transfer_bill_courier_id',function($row){
                    return $row->courier->emp_firstname.' '.$row->courier->emp_lastname;
                })
                ->editColumn('tranfer_by_employee_id',function($row){
                    return $row->employee_creeate->emp_firstname.' '.$row->employee_creeate->emp_lastname;
                })
                ->editColumn('courierPhone', function($row){
                    return $row->courier->emp_phone;
                })
                ->editColumn('transfer_bill_status', function($row){
                    if($row->transfer_bill_status == 'TransferToCourier' || $row->transfer_bill_status == 'sendingCOD'){
                        $Transfers = Transfer::where('transfer_bill_id',$row->id)->get();
                        $btnstatus = 1;
                        foreach ($Transfers as $Transfer) {
                            if($Transfer->transfer_status == 'TransferToCourier'){
                                $btnstatus = 0;
                            }
                        }
                        if($btnstatus == 1){
                            $status = "ดำเนินการสำเร็จ";
                        }else{
                            $status = "ดำเนินการจัดส่ง";
                        }
                    }else if($row->transfer_bill_status == 'done'){
                        $status = "ปิดบิลสำเร็จ";
                    }
                    return $status;
                })
                ->editColumn('CODamount', function($row){
                    $Transfers = Transfer::where('transfer_bill_id',$row->id)->where('transfer_status', 'CustomerResiveDone')->orwhere('transfer_bill_id',$row->id)->where('transfer_status', 'CustomerResiveDoneReturn')->get();
                    $COD = 0;
                    foreach ($Transfers as $Transfer) {
                        if(strpos($Transfer->transfer_status, 'Return') !== false){
                            $SubTrackings = SubTracking::where('subtracking_tracking_id', $Transfer->transfer_tracking_id)->get();
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_price;;
                            }
                        }else{
                            $COD += $Transfer->cod_amount;
                        }
                    }
                    return number_format($COD,2);
                })
                ->editColumn('created_at', function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->addColumn('action', function($row) use($employee){
                    $btn = '<a href="/printDeleveryReport/'.$row->id.'" class="btn btn-warning btn-sm" target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);
            }else if($request->report_type == '9'){
                $user = Auth::user();
                $employee = Employee::where('id',$user->employee_id)->first();
                $TransferDropCenterBills = TransferDropCenterBill::where('transfer_sender_id',$request->dropcenter)->whereDate('created_at','>=', $request->selectdateFrom)->whereDate('created_at','<=', $request->selectdateTo)->orderby('created_at', 'DESC')->get();
                return Datatables::of($TransferDropCenterBills)
                ->addIndexColumn()
                ->editColumn('created_at',function($row){
                    return date_format($row->created_at,"d/m/Y H:i");
                })
                ->editColumn('tranfer_employee_sender_id',function($row){
                    if($row->transfer_bill_status == "receive-done"){
                        return $row->Employee_sender->emp_firstname.'-'.$row->Employee_driver->emp_firstname.'-'.$row->Employee->emp_firstname;
                    }else{
                        return $row->Employee_sender->emp_firstname.'-'.$row->Employee_driver->emp_firstname.'-(ยังไม่ได้รับพัสดุ)';
                    }
                })
                ->editColumn('note', function($row){
                    return $row->dc_sender->drop_center_name_initial.' - '.$row->dc_receiver->drop_center_name_initial;
                })
                ->addColumn('action', function($row) use($employee){
                    $btn = '<a href="/linehallDetail/'.$row->id.'" target="_blank" class="btn btn-warning btn-sm" target="blank"><i class="fa fa-print" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action' => 'action'])
                ->make(true);
            }
        }
    }
}
