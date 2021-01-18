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
use DB;
use Validator;
use PDF;
use Auth;
use DataTables;

class TransfersController extends Controller {

    public function getTransferByCourier($id = null) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($id){
            $date = date('Y-m-d');
            $TranserBillList = TranserBill::where('transfer_bill_courier_id', $id)->where('created_at', 'like', $date.'%')->orderby('created_at','desc')->get();
            $TranserBillHasBeenOpen = TranserBill::where('transfer_bill_courier_id', $id)->where('transfer_bill_status', 'TransferToCourier')->orwhere('transfer_bill_courier_id', $id)->where('transfer_bill_status', 'sendingCOD')->first();
            $employeecurier = Employee::where('id', $id)->first();
            $transfers = Transfer::where('transfer_courier_id', $id)->where('transfer_status', 'new')->orwhere('transfer_courier_id', $id)->where('transfer_status', 'newReturn')->get();
            $TransfersDuplicates = TransfersDuplicate::where('duplicate_courier_id', $id)->orderby('created_at','desc')->get();

            // $parcelReceiveDonelList = Transfer::where('transfer_courier_id', $id) //ส่งไปเพื่อแสดงจำนวน
            // ->where('transfer_status', "CustomerResiveDone")
            // ->orwhere('transfer_status', "done")
            // ->get();

            // $parcelWrongList = Transfer::where('transfer_courier_id', $id) //ส่งไปเพื่อแสดงจำนวน
            // ->where('transfer_status', "SendingFalse")
            // ->get();

            return view('Transfers.create_transfer_parcel_for_courier',compact(['transfers','employeecurier','employee','TranserBillList','TransfersDuplicates','TranserBillHasBeenOpen']));
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function addTrackingToCourier(Request $request) {
        // $tracking = Tracking::where('tracking_no',$request->tracking_no)
        // ->where('tracking_status','done')
        // ->first();
        $length = strlen($request->tracking_no);
        $substr_tracking_no = substr($request->tracking_no, 0,15);
        $substr_subtracking_no = substr($request->tracking_no, 15,$length);

        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $tracking = Tracking::where('tracking_no', $substr_tracking_no)->first();
        // dd($tracking->Con->track_id);
        if($tracking){
            $Booking = Booking::find($tracking->tracking_booking_id);
            if (strpos($tracking->tracking_status, 'Return') !== false) {
                $customerresive = Customer::find($Booking->booking_sender_id);
                $transfer_status = "newReturn";
                $tracking_status_id = 10;
                $PacelCare_status = 17;
                $showRtnLog = "(RTN)";
            }else{
                $customerresive = Customer::find($tracking->tracking_receiver_id);
                $transfer_status = "new";
                $tracking_status_id = 5;
                $PacelCare_status = 6;
                $showRtnLog = "";
            }
            $PostCode = PostCode::where('postcode', $customerresive->cust_postcode)->first();
            if($tracking->tracking_status == "done" && $tracking->Con->recive_dc == $employee->emp_branch_id || $tracking->tracking_status == "ReceiveDone" && $tracking->Con->to_dc == $employee->emp_branch_id || $tracking->tracking_status == "ReceiveDoneReturn"  && $tracking->Con->recive_dc == $employee->emp_branch_id){

                $CourierArea = CourierArea::where('post_code_id', $PostCode->postcode)->where('employee_id', $request->courier_id)->first();
                if(!empty($CourierArea)){
                    $Transfer = Transfer::where('transfer_booking_id', $tracking->tracking_booking_id)->where('transfer_tracking_id',$tracking->id)->where('transfer_status', '!=', 'CustomerResiveDone')->where('transfer_status', '!=', 'ReturnDone')->where('transfer_status', '!=', 'ReturnBackToDC')->first();
                    if(empty($Transfer)){
                        $subTrackingList = SubTracking::where('subtracking_tracking_id', $tracking->id)->get();
                        $parcel_amount = count($subTrackingList);
                        $cod_amount = 0;
                        $findesubtrackid = 0;
                        foreach($subTrackingList as $subTracking){
                            $cod_amount += $subTracking->subtracking_cod;
                            if($subTracking->subtracking_under_tracking_id == $substr_subtracking_no){
                                $findesubtrackid = 1;
                            }
                        }
                        // dd($findesubtrackid);
                        if($findesubtrackid == 1){
                            // $ParcelWrong = ParcelWrongs::where('wrong_tracking_id', $tracking->id)->where('wrong_status', 'true')->first();
                            // if(!empty($ParcelWrong)){
                            //     $transfer_status = "newReturn";
                            //     $tracking_status_id = 10;
                            // }else{
                            //     $transfer_status = "new";
                            //     $tracking_status_id = 5;
                            // }
                            Transfer::create([
                                'transfer_booking_id' => $tracking->tracking_booking_id,
                                'transfer_courier_id' => $request->courier_id,
                                'transfer_status' => $transfer_status,
                                'transfer_tracking_id' => $tracking->id,
                                'parcel_received_amount' => $substr_subtracking_no,
                                'parcel_amount' => $parcel_amount,
                                'cod_amount' => $cod_amount,
                            ]);
                            
                            $date = date('Y-m-d H:i:s');
                            $TrackingsLogs = TrackingsLog::create([
                                'tracking_no' => $tracking->tracking_no.$showRtnLog, 
                                'tracking_receiver_id' => $tracking->tracking_receiver_id,
                                'tracking_status_id' => $tracking_status_id, 
                                'tracking_branch_id_dc' => $Booking->booking_branch_id, 
                                'tracking_branch_id_sub_dc' => $PostCode->drop_center_id,
                                'tracking_date' => $date
                            ]);
                        }else{
                            TransfersDuplicate::create([
                                'duplicate_tracking_no' => $substr_tracking_no, 
                                'duplicate_courier_id' => $request->courier_id,
                                'duplicate_status' => 1
                            ]);
                            return redirect()->back();
                        }
                    }else{
                        $subTrackingList = SubTracking::where('subtracking_tracking_id', $tracking->id)->get();
                        $parcel_amount = count($subTrackingList);
                        $cod_amount = 0;
                        $findesubtrackid = 0;
                        foreach($subTrackingList as $subTracking){
                            $cod_amount += $subTracking->subtracking_cod;
                            if($subTracking->subtracking_under_tracking_id == $substr_subtracking_no){
                                $findesubtrackid = 1;
                            }
                        }
                        if($findesubtrackid == 1){
                            $subtrackingarray = explode(",",$Transfer->parcel_received_amount);
                            $arrrayDuplicate = 0;
                            for($i = 0; $i < count($subtrackingarray); $i++){
                                if($substr_subtracking_no == $subtrackingarray[$i]){
                                    $arrrayDuplicate = 1;
                                }
                            }

                            if($arrrayDuplicate == 0){
                                $Transfer->update([
                                    'parcel_received_amount' => $Transfer->parcel_received_amount.','.$substr_subtracking_no,
                                ]);

                                $Transfer = Transfer::where('transfer_booking_id', $tracking->tracking_booking_id)->where('transfer_tracking_id',$tracking->id)->orderby('created_at', 'Desc')->first();
                                $subtrackingarray = explode(",",$Transfer->parcel_received_amount);
                                if(count($subtrackingarray) == $Transfer->parcel_amount){
                                    // dd(count($subtrackingarray));
                                    $trackingupstatus = Tracking::find($tracking->id);
                                    if (strpos($trackingupstatus->tracking_status, 'Return') !== false) {
                                        $tracking_status = 'transferDoingReturn';
                                    }else{
                                        $tracking_status = 'transferDoing';
                                    }
                                    $trackingupstatus->update([
                                        'tracking_status' => $tracking_status
                                    ]);
                                    $PacelCare = PacelCare::create([
                                        'tracking_id' => $trackingupstatus->id, 
                                        'doing_by' => $employee->id,
                                        'branch_id' => $employee->emp_branch_id, 
                                        'status' => $PacelCare_status, 
                                        'ref_no' => null
                                    ]);
                                }
                            }else{
                                TransfersDuplicate::create([
                                    'duplicate_tracking_no' => $substr_tracking_no, 
                                    'duplicate_courier_id' => $request->courier_id,
                                    'duplicate_status' => 2
                                ]);
                            }
                        }else{
                            TransfersDuplicate::create([
                                'duplicate_tracking_no' => $substr_tracking_no, 
                                'duplicate_courier_id' => $request->courier_id,
                                'duplicate_status' => 1
                            ]);
                        }
                        return redirect()->to('/getTransferByCourier/'.$request->courier_id);
                    }
                    $Transfer = Transfer::where('transfer_booking_id', $tracking->tracking_booking_id)->where('transfer_tracking_id',$tracking->id)->orderby('created_at', 'Desc')->first();
                    $subtrackingarray = explode(",",$Transfer->parcel_received_amount);
                    if(count($subtrackingarray) == $Transfer->parcel_amount){
                        $trackingupstatus = Tracking::find($tracking->id);
                        if (strpos($trackingupstatus->tracking_status, 'Return') !== false) {
                            $tracking_status = 'transferDoingReturn';
                        }else{
                            $tracking_status = 'transferDoing';
                        }
                        $trackingupstatus->update([
                            'tracking_status' => $tracking_status
                        ]);

                        $PacelCare = PacelCare::create([
                            'tracking_id' => $trackingupstatus->id, 
                            'doing_by' => $employee->id,
                            'branch_id' => $employee->emp_branch_id, 
                            'status' => $PacelCare_status, 
                            'ref_no' => null
                        ]);
                    }
                    return redirect()->to('/getTransferByCourier/'.$request->courier_id);
                }else{
                    TransfersDuplicate::create([
                        'duplicate_tracking_no' => $substr_tracking_no, 
                        'duplicate_courier_id' => $request->courier_id,
                        'duplicate_status' => 6
                    ]);
                    // alert()->error('ขออภัย', 'tracking นี้ ไม่ตรงกับพื้นที่รับผิดชอบของพนักงาน')->showConfirmButton('ตกลง', '#3085d6');
                    return redirect()->back();
                }
            }else{
                $tracking = Tracking::where('tracking_no',$substr_tracking_no)->first();
                if($tracking){
                    // return $tracking;
                    if($tracking->tracking_status == 'transferDoing') {
                        TransfersDuplicate::create([
                            'duplicate_tracking_no' => $substr_tracking_no, 
                            'duplicate_courier_id' => $request->courier_id,
                            'duplicate_status' => 2
                        ]);
                        // alert()->error('ขออภัย', 'tracking นี้ อยู่ในสถานะทำเบิกจ่ายแล้ว')->showConfirmButton('ตกลง', '#3085d6');
                        return redirect()->back();
                    }else if($tracking->tracking_status == 'TransferToCourier') {
                        TransfersDuplicate::create([
                            'duplicate_tracking_no' => $substr_tracking_no, 
                            'duplicate_courier_id' => $request->courier_id,
                            'duplicate_status' => 3
                        ]);
                        // alert()->error('ขออภัย', 'tracking นี้ อยู่ในสถานะทำเบิกจ่ายแล้ว')->showConfirmButton('ตกลง', '#3085d6');
                        return redirect()->back();
                    }else if($tracking->tracking_status == 'CustomerResiveDone') {
                        TransfersDuplicate::create([
                            'duplicate_tracking_no' => $substr_tracking_no, 
                            'duplicate_courier_id' => $request->courier_id,
                            'duplicate_status' => 4
                        ]);
                        // alert()->error('ขออภัย', 'tracking นี้ ถูกส่งให้ลูกค้าปลายทางสำเร็จแล้ว')->showConfirmButton('ตกลง', '#3085d6');
                        return redirect()->back();
                    }else{
                        TransfersDuplicate::create([
                            'duplicate_tracking_no' => $substr_tracking_no, 
                            'duplicate_courier_id' => $request->courier_id,
                            'duplicate_status' => 5
                        ]);
                        return redirect()->back();
                    }
                }
            }
        }else{
            TransfersDuplicate::create([
                'duplicate_tracking_no' => $substr_tracking_no, 
                'duplicate_courier_id' => $request->courier_id,
                'duplicate_status' => 1
            ]);
            return redirect()->back();
        }
    }

    public function saveTransferToCourier(Request $request, $id = null) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $courier = Employee::where('id', $id)->first();
        $transfers = Transfer::where('transfer_courier_id',$id)->where('transfer_status','new')->orwhere('transfer_courier_id',$id)->where('transfer_status','newReturn')->get();

        if(count($transfers) > 0){
            $date = date('Y-m-d');
            $create_courierBill = TranserBill::where('transfer_bill_courier_id', $transfers[0]->transfer_courier_id)->where('transfer_bill_status', '!=', 'done')->where('created_at','like',$date.'%')->first();
            if($create_courierBill == null){
                $transfer_bill_no = date('Ymd').rand(1000,9999);
                $create_courierBill = TranserBill::create([
                    'transfer_bill_no' => $transfer_bill_no,
                    'transfer_bill_courier_id' => $transfers[0]->transfer_courier_id, // dc_receiver 
                    'transfer_bill_status' => 'TransferToCourier',
                    'tranfer_driver_sender_numberplate' => $request->tranfer_driver_sender_numberplate,
                    'tranfer_by_employee_id' => $employee->id,
                    'tranfer_bill_branch_id' => $employee->emp_branch_id
                ]);


                foreach ($transfers as $transfer) {
                    $transfer_status = "TransferToCourier";
                    $logstatus = 6;
                    $PacelCare_status = 7;
                    $rtn_show = "";

                    if (strpos($transfer->transfer_status, 'Return') !== false) {
                        $transfer_status = "TransferToCourierReturn";
                        $logstatus = 12;
                        $PacelCare_status = 18;
                        $rtn_show = "(RTN)";
                    }
                    $transfer->update([
                        'transfer_status' => $transfer_status,
                        'transfer_bill_id' => $create_courierBill->id,
                        'transfer_branch_id' => $employee->emp_branch_id
                    ]);

                    $tracking = Tracking::find($transfer->transfer_tracking_id);
                    $Booking = Booking::find($tracking->tracking_booking_id);
                    $customerresive = Customer::find($tracking->tracking_receiver_id);
                    $PostCode = PostCode::where('postcode', $customerresive->cust_postcode)->first();
                    $tracking->update([
                        'tracking_status' => $transfer_status
                    ]);
                    $date = date('Y-m-d H:i:s');
                    $TrackingsLogs = TrackingsLog::create([
                        'tracking_no' => $tracking->tracking_no.$rtn_show, 
                        'tracking_receiver_id' => $tracking->tracking_receiver_id,
                        'tracking_status_id' => $logstatus, 
                        'tracking_branch_id_dc' => $Booking->booking_branch_id, 
                        'tracking_branch_id_sub_dc' => $PostCode->drop_center_id,
                        'tracking_date' => $date,
                        'tracking_cause' => $transfer->id
                    ]);

                    $PacelCare = PacelCare::create([
                        'tracking_id' => $transfer->transfer_tracking_id, 
                        'doing_by' => $employee->id,
                        'branch_id' => $employee->emp_branch_id, 
                        'status' => $PacelCare_status, 
                        'ref_no' => $transfer->id
                    ]);
                }
            }else{
                $create_courierBill->update([
                    'transfer_bill_status' => 'TransferToCourier',
                ]);

                foreach ($transfers as $transfer) {
                    $transfer_status = "TransferToCourier";
                    $logstatus = 6;
                    $PacelCare_status = 7;
                    $rtn_show = "";

                    if (strpos($transfer->transfer_status, 'Return') !== false) {
                        $transfer_status = "TransferToCourierReturn";
                        $logstatus = 12;
                        $PacelCare_status = 18;
                        $rtn_show = "(RTN)";
                    }
                    $transfer->update([
                        'transfer_status' => $transfer_status,
                        'transfer_bill_id' => $create_courierBill->id,
                        'transfer_branch_id' => $employee->emp_branch_id
                    ]);

                    $tracking = Tracking::find($transfer->transfer_tracking_id);
                    $Booking = Booking::find($tracking->tracking_booking_id);
                    $customerresive = Customer::find($tracking->tracking_receiver_id);
                    $PostCode = PostCode::where('postcode', $customerresive->cust_postcode)->first();
                    $tracking->update([
                        'tracking_status' => $transfer_status
                    ]);
                    $date = date('Y-m-d H:i:s');
                    $TrackingsLogs = TrackingsLog::create([
                        'tracking_no' => $tracking->tracking_no.$rtn_show, 
                        'tracking_receiver_id' => $tracking->tracking_receiver_id,
                        'tracking_status_id' => $logstatus, 
                        'tracking_branch_id_dc' => $Booking->booking_branch_id,
                        'tracking_branch_id_sub_dc' => $PostCode->drop_center_id,
                        'tracking_date' => $date,
                        'tracking_cause' => $transfer->id
                    ]);

                    $PacelCare = PacelCare::create([
                        'tracking_id' => $transfer->transfer_tracking_id, 
                        'doing_by' => $employee->id,
                        'branch_id' => $employee->emp_branch_id, 
                        'status' => $PacelCare_status, 
                        'ref_no' => $transfer->id
                    ]);
                }
            }

            $TransfersDuplicates = TransfersDuplicate::where('duplicate_courier_id', $id)->get();
            foreach ($TransfersDuplicates as $TransfersDuplicate) {
                $TransfersDuplicate->delete();
            }

            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            // return redirect()->to('/getTransferByCourier/'.$id);
            return redirect()->back();
        }else{
            alert()->error('ขออภัย', 'ไม่พบการปรับปรุงข้อมูลนี้')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function getTransferByDropCenter($id = null) {
        if($id){
            // return $id;
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $dropcenter = DropCenter::where('id', $id)->first();
            $TranferDropCenterDuplicates = TranferDropCenterDuplicate::where('duplicate_transfer_dropcenter_id', $id)->where('duplicate_dropcenter_sender_id', $user->emp_branch_id)->orderby('id', 'Desc')->get();
            // return $dropcenter;
            if($dropcenter){
                $linehauls = Employee::where('emp_position','พนักงานส่งพัสดุ(Line Haul)')->get();
                $transfers = TransferDropCenter::where('transfer_dropcenter_id',$dropcenter->id)
                ->where('transfer_dropcenter_status','new')
                ->orwhere('transfer_dropcenter_status','newReturn')
                ->get();
                $date = date('Y-m-d');
                $TransferDropCenterBills = TransferDropCenterBill::where('transfer_sender_id',$user->emp_branch_id)
                ->where('transfer_recriver_id',$id)
                ->where('created_at', 'like', $date.'%')
                ->get();
                // dd($transfers);

                if(count($transfers) > 0){
                    $parcelReceiveDonelList = TransferDropCenter::where('transfer_dropcenter_id', $id)
                    ->where('transfer_dropcenter_status', "ReceiveDone")
                    ->get();
    
                    $parcelWrongList = TransferDropCenter::where('transfer_dropcenter_id', $id)
                    ->where('transfer_dropcenter_status', "ParcelWrong")
                    ->get();
    
                    return view('Transfers.create_transfer_parcel_for_drop_center',compact(['transfers','dropcenter','parcelReceiveDonelList','parcelWrongList','TransferDropCenterBills','TranferDropCenterDuplicates','employee','linehauls']));
                }else{
                    return view('Transfers.create_transfer_parcel_for_drop_center',compact('dropcenter','TransferDropCenterBills','TranferDropCenterDuplicates','employee','linehauls'));
                }

            }else{
                alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->back();
            }
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function linehallDetail($id = null){
        $TransferDropCenterBills = TransferDropCenterBill::find($id);
        $DropCentersender = DropCenter::find($TransferDropCenterBills->transfer_sender_id);
        $DropCenterresive = DropCenter::find($TransferDropCenterBills->transfer_recriver_id);
        $drivername = Employee::where('id',$TransferDropCenterBills->tranfer_driver_sender_name)->first();
        $TransferDropCenters = TransferDropCenter::where('transfer_bill_id_ref', $TransferDropCenterBills->id)->get();
        // dd($TransferDropCenterBills->tranfer_employee_sender_id);
        $pdf = PDF::loadView('/Transfers.linehallDetail',compact(['TransferDropCenterBills','DropCentersender','DropCenterresive','TransferDropCenters','drivername']))->setPaper('A4', 'portrait');
        return $pdf->stream();
    }
    
    public function addTrackingToDropCenter(Request $request) {
        $length = strlen($request->tracking_no);
        $substr_tracking_no = substr($request->tracking_no, 0,15);
        $substr_subtracking_no = substr($request->tracking_no, 15,$length);
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();

        $tracking = Tracking::where('tracking_no',$substr_tracking_no)->where('tracking_status','done')->orwhere('tracking_no',$substr_tracking_no)->where('tracking_status','transferDCDoing')->orwhere('tracking_no',$substr_tracking_no)->where('tracking_status','ReturnBack')->orwhere('tracking_no',$substr_tracking_no)->where('tracking_status','transferDCDoingReturn')->first();
        if($tracking){
            // dd('2');
            if($tracking->tracking_status == 'done' || $tracking->tracking_status == 'transferDCDoing'){
                //กรณีส่งไป
                $TransferDropCenter = TransferDropCenter::where('transfer_dropcenter_tracking_no',$substr_tracking_no)->where('transfer_dropcenter_sender_id',$employee->emp_branch_id)->first();
                if(!empty($TransferDropCenter)){
                    // dd('1');
                    $subtrackingarray = explode(",",$TransferDropCenter->parcel_received_amount);
                    // dd(count($subtrackingarray));
                    $arrrayDuplicate = 0;
                    for($i = 0; $i < count($subtrackingarray); $i++){
                        if($substr_subtracking_no == $subtrackingarray[$i]){
                            $arrrayDuplicate = 1;
                        }
                    }

                    if($arrrayDuplicate == 0){
                        $SubTracking = SubTracking::where('subtracking_tracking_id', $TransferDropCenter->transfer_dropcenter_tracking_id)->where('subtracking_under_tracking_id', $substr_subtracking_no)->first();
                        if(!empty($SubTracking)){
                            $TransferDropCenter->update([
                                'parcel_received_amount' => $TransferDropCenter->parcel_received_amount.','.$substr_subtracking_no,
                            ]);
                        }else{
                            $TrackingsLogs = TranferDropCenterDuplicate::create([
                                'duplicate_tracking_no' => $substr_tracking_no, 
                                'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                                'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                                'duplicate_status' => 4
                            ]);
                        }
                    }else{
                        $TrackingsLogs = TranferDropCenterDuplicate::create([
                            'duplicate_tracking_no' => $substr_tracking_no, 
                            'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                            'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                            'duplicate_status' => 1
                        ]);
                    }
                    return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
                }else{
                    $tracking = Tracking::where('tracking_no',$substr_tracking_no)->where('tracking_status','done')->first();
                    if($tracking){
                        $SubTrackings = SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                        // dd($SubTrackings);
                        $customers = Customer::find($tracking->tracking_receiver_id);
                        $PostCodes = PostCode::where('postcode',$customers->cust_postcode)->first();
                        if(!empty($PostCodes)){
                            $post = $PostCodes->drop_center_id;
                        }else{
                            $post = "";
                        }
                        $parcel_amount = count($SubTrackings);
                        $SubTrackingshave = 0;
                        foreach ($SubTrackings as $SubTracking) {
                            if($SubTracking->subtracking_under_tracking_id == $substr_subtracking_no){
                                $SubTrackingshave = 1;
                            }
                        }
                        // dd($post.' '.$request->dropcenter_id);
                        if($post  == $request->dropcenter_id){
                            // dd($SubTrackingshave);
                            if ($SubTrackingshave == '1') {
                                TransferDropCenter::create([
                                    'transfer_dropcenter_booking_id' => $tracking->tracking_booking_id,
                                    'transfer_dropcenter_id' => $request->dropcenter_id, // dc_receiver 
                                    'transfer_dropcenter_status' => 'new',
                                    'transfer_dropcenter_tracking_id' => $tracking->id,
                                    'transfer_dropcenter_sender_id' => $user->emp_branch_id,  
                                    'transfer_dropcenter_tracking_no' => $tracking->tracking_no,
                                    'parcel_received_amount' => $substr_subtracking_no,
                                    'parcel_amount' => $parcel_amount
                                ]);
            
                                $tracking->update([
                                    'tracking_status' => 'transferDCDoing'
                                ]);

                                $PacelCare = PacelCare::create([
                                    'tracking_id' => $tracking->id, 
                                    'doing_by' => $employee->id,
                                    'branch_id' => $employee->emp_branch_id, 
                                    'status' => 2, 
                                    'ref_no' => null
                                ]);
            
                                return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
                            } else {
                                $TrackingsLogs = TranferDropCenterDuplicate::create([
                                    'duplicate_tracking_no' => $substr_tracking_no, 
                                    'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                                    'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                                    'duplicate_status' => 4
                                ]);
                                return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
                            }
                        }else{
                            $TrackingsLogs = TranferDropCenterDuplicate::create([
                                'duplicate_tracking_no' => $substr_tracking_no, 
                                'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                                'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                                'duplicate_status' => 2
                            ]);
                            // alert()->error('ขออภัย', 'ปลายทางของ tracking นี้ ไม่ถูกต้อง')->showConfirmButton('ตกลง', '#3085d6');
                            return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
                            // return redirect()->back();
                        }
                    }else{
                        $TrackingsLogs = TranferDropCenterDuplicate::create([
                            'duplicate_tracking_no' => $substr_tracking_no, 
                            'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                            'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                            'duplicate_status' => 3
                        ]);
                        // alert()->error('ขออภัย', 'tracking นี้ ไม่อยู่ในสถานะเบิกจ่ายได้')->showConfirmButton('ตกลง', '#3085d6');
                        return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
                        // return redirect()->back();
                    }
                }
            }else if($tracking->tracking_status == 'ReturnBack' || $tracking->tracking_status == 'transferDCDoingReturn'){
                //กรณีส่งกลับ
                $TransferDropCenter = TransferDropCenter::where('transfer_dropcenter_tracking_no',$substr_tracking_no)->where('transfer_dropcenter_sender_id',$employee->emp_branch_id)->first();
                if(!empty($TransferDropCenter)){
                    $subtrackingarray = explode(",",$TransferDropCenter->parcel_received_amount);
                    $arrrayDuplicate = 0;
                    for($i = 0; $i < count($subtrackingarray); $i++){
                        if($substr_subtracking_no == $subtrackingarray[$i]){
                            $arrrayDuplicate = 1;
                        }
                    }
                    if($arrrayDuplicate == 0){
                        $SubTracking = SubTracking::where('subtracking_tracking_id', $TransferDropCenter->transfer_dropcenter_tracking_id)->where('subtracking_under_tracking_id', $substr_subtracking_no)->first();
                        if(!empty($SubTracking)){
                            $TransferDropCenter->update([
                                'parcel_received_amount' => $TransferDropCenter->parcel_received_amount.','.$substr_subtracking_no,
                            ]);
                        }else{
                            $TrackingsLogs = TranferDropCenterDuplicate::create([
                                'duplicate_tracking_no' => $substr_tracking_no, 
                                'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                                'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                                'duplicate_status' => 4
                            ]);
                        }
                    }else{
                        $TrackingsLogs = TranferDropCenterDuplicate::create([
                            'duplicate_tracking_no' => $substr_tracking_no, 
                            'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                            'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                            'duplicate_status' => 1
                        ]);
                    }
                    return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
                }else{
                    $tracking = Tracking::where('tracking_no',$substr_tracking_no)->where('tracking_status','ReturnBack')->first();
                    if($tracking){
                        $SubTrackings = SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                        $Booking = Booking::find($tracking->tracking_booking_id);
                        if(!empty($Booking)){
                            $post = $Booking->booking_branch_id;
                        }else{
                            $post = "";
                        }
                        $parcel_amount = count($SubTrackings);
                        $SubTrackingshave = 0;
                        foreach ($SubTrackings as $SubTracking) {
                            if($SubTracking->subtracking_under_tracking_id == $substr_subtracking_no){
                                $SubTrackingshave = 1;
                            }
                        }
                        if($post  == $request->dropcenter_id){
                            if ($SubTrackingshave == '1') {
                                TransferDropCenter::create([
                                    'transfer_dropcenter_booking_id' => $tracking->tracking_booking_id,
                                    'transfer_dropcenter_id' => $request->dropcenter_id, // dc_receiver 
                                    'transfer_dropcenter_status' => 'newReturn',
                                    'transfer_dropcenter_tracking_id' => $tracking->id,
                                    'transfer_dropcenter_sender_id' => $user->emp_branch_id,  
                                    'transfer_dropcenter_tracking_no' => $tracking->tracking_no,
                                    'parcel_received_amount' => $substr_subtracking_no,
                                    'parcel_amount' => $parcel_amount
                                ]);
            
                                $tracking->update([
                                    'tracking_status' => 'transferDCDoingReturn'
                                ]);

                                $PacelCare = PacelCare::create([
                                    'tracking_id' => $tracking->id, 
                                    'doing_by' => $employee->id,
                                    'branch_id' => $employee->emp_branch_id, 
                                    'status' => 13, 
                                    'ref_no' => null
                                ]);
            
                                return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
                            } else {
                                $TrackingsLogs = TranferDropCenterDuplicate::create([
                                    'duplicate_tracking_no' => $substr_tracking_no, 
                                    'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                                    'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                                    'duplicate_status' => 4
                                ]);
                                return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
                            }
                        }else{
                            $TrackingsLogs = TranferDropCenterDuplicate::create([
                                'duplicate_tracking_no' => $substr_tracking_no, 
                                'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                                'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                                'duplicate_status' => 2
                            ]);
                            // alert()->error('ขออภัย', 'ปลายทางของ tracking นี้ ไม่ถูกต้อง')->showConfirmButton('ตกลง', '#3085d6');
                            return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
                            // return redirect()->back();
                        }
                    }else{
                        $TrackingsLogs = TranferDropCenterDuplicate::create([
                            'duplicate_tracking_no' => $substr_tracking_no, 
                            'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                            'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                            'duplicate_status' => 3
                        ]);
                        // alert()->error('ขออภัย', 'tracking นี้ ไม่อยู่ในสถานะเบิกจ่ายได้')->showConfirmButton('ตกลง', '#3085d6');
                        return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
                        // return redirect()->back();
                    }
                }

            }
        }else{
            $TrackingsLogs = TranferDropCenterDuplicate::create([
                'duplicate_tracking_no' => $substr_tracking_no, 
                'duplicate_transfer_dropcenter_id' => $request->dropcenter_id,
                'duplicate_dropcenter_sender_id' => $user->emp_branch_id,
                'duplicate_status' => 3
            ]);
            // alert()->error('ขออภัย', 'tracking นี้ ไม่อยู่ในสถานะเบิกจ่ายได้')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->to('/getTransferByDropCenter/'.$request->dropcenter_id);
        }
    }

    public function saveTransferToDropCenter(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'tranfer_driver_sender_name' => 'required',
            'tranfer_driver_sender_numberplate' => 'required',
            'tranfer_driver_sender_phone' => 'required'
        ]);
        if ($validator->fails()) {
            alert()->error('ขออภัย', 'กรอกข้อมูลไม่ครบ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($user){
            $transfers = TransferDropCenter::where('transfer_dropcenter_id',$request->id)
            ->where('transfer_dropcenter_sender_id', $user->emp_branch_id)
            ->where('transfer_dropcenter_status','new')
            ->orwhere('transfer_dropcenter_sender_id', $user->emp_branch_id)
            ->where('transfer_dropcenter_status','newReturn')
            ->get();
            if(count($transfers) > 0){
                $countBillNo = count(TransferDropCenterBill::where('created_at', 'like', date("Y-m-d").'%')->get())+1;
                $transfer_bill_no = date("Ymd").$countBillNo;
                $transfer_sender_id = $user->emp_branch_id;
                $transfer_recriver_id = $request->id;
                $transfer_bill_status = "sending";

                $createTransferBill = TransferDropCenterBill::create([
                    'transfer_bill_no' => $transfer_bill_no,
                    'transfer_sender_id' => $transfer_sender_id,
                    'transfer_recriver_id' => $transfer_recriver_id,
                    'transfer_bill_status' => $transfer_bill_status,
                    'tranfer_driver_sender_name' => $request->tranfer_driver_sender_name,
                    'tranfer_driver_sender_numberplate' => $request->tranfer_driver_sender_numberplate,
                    'tranfer_driver_sender_phone' => $request->tranfer_driver_sender_phone,
                    'tranfer_employee_sender_id' => $employee->id
                ]);
    
                foreach ($transfers as $transfer) {
                    if($transfer->transfer_dropcenter_status == 'newReturn'){
                        $transfer->update([
                            'transfer_dropcenter_status' => 'TransferToDropCenterReturn',
                            'transfer_bill_no_ref' => $createTransferBill->transfer_bill_no,
                            'transfer_bill_id_ref' => $createTransferBill->id
                        ]);

                        $tracking = Tracking::find($transfer->transfer_dropcenter_tracking_id);
                        $tracking->update([
                            'tracking_status' => 'TransferToDropCenterReturn',
                            'tracking_send_status' => NULL,
                            'send_pick_time' => NULL
                        ]);

                        $PacelCare = PacelCare::create([
                            'tracking_id' => $tracking->id, 
                            'doing_by' => $employee->id,
                            'branch_id' => $employee->emp_branch_id, 
                            'status' => 14, 
                            'ref_no' => $transfer->id
                        ]);

                        $date = date('Y-m-d H:i:s');
                        $TrackingsLogs = TrackingsLog::create([
                            'tracking_no' => $transfer->transfer_dropcenter_tracking_no.'(RTN)', 
                            'tracking_receiver_id' => $tracking->booking->booking_sender_id,
                            'tracking_status_id' => 8, 
                            'tracking_branch_id_dc' => $transfer->transfer_dropcenter_sender_id, 
                            'tracking_branch_id_sub_dc' => $transfer->transfer_dropcenter_id,
                            'tracking_date' => $date
                        ]);

                        $subTrackings = SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                        foreach($subTrackings as $subTracking){
                            $subTracking->update([
                                'subtracking_status' => 'TransferToDropCenterReturn',
                            ]);
                        }

                    }else if($transfer->transfer_dropcenter_status == 'new'){
                        $transfer->update([
                            'transfer_dropcenter_status' => 'TransferToDropCenter',
                            'transfer_bill_no_ref' => $createTransferBill->transfer_bill_no,
                            'transfer_bill_id_ref' => $createTransferBill->id
                        ]);

                        $tracking = Tracking::find($transfer->transfer_dropcenter_tracking_id);
                        $date = date('Y-m-d H:i:s');
                        $tracking->update([
                            'tracking_status' => 'TransferToDropCenter',
                        ]);

                        $TrackingsLogs = TrackingsLog::create([
                            'tracking_no' => $transfer->transfer_dropcenter_tracking_no, 
                            'tracking_receiver_id' => $tracking->tracking_receiver_id,
                            'tracking_status_id' => 2, 
                            'tracking_branch_id_dc' => $transfer->transfer_dropcenter_sender_id, 
                            'tracking_branch_id_sub_dc' => $transfer->transfer_dropcenter_id,
                            'tracking_date' => $date
                        ]);

                        $PacelCare = PacelCare::create([
                            'tracking_id' => $tracking->id, 
                            'doing_by' => $employee->id,
                            'branch_id' => $employee->emp_branch_id, 
                            'status' => 3, 
                            'ref_no' => $transfer->id
                        ]);

                        $subTrackings = SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                        foreach($subTrackings as $subTracking){
                            $subTracking->update([
                                'subtracking_status' => 'TransferToDropCenter',
                            ]);
                        }
                    }
                }
                
                $TranferDropCenterDuplicates = TranferDropCenterDuplicate::where('duplicate_transfer_dropcenter_id', $request->id)->where('duplicate_dropcenter_sender_id', $user->emp_branch_id)->get();
                foreach ($TranferDropCenterDuplicates as $TranferDropCenterDuplicate) {
                    $TranferDropCenterDuplicate->delete();
                }

                alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->to('/getTransferByDropCenter/'.$request->id);
    
            }else{
                alert()->error('ขออภัย', 'ไม่พบรายที่ต้องการส่ง')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->back();
            }
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function getParcelListFromOtherDC($id = null) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($id){
            $date = date('Y-m-d');
            $parcelFromOtherList = TransferDropCenterBill::where('transfer_recriver_id',$id)->where('transfer_bill_status', 'sending')->orwhere('transfer_recriver_id',$id)->where('updated_at', 'like', $date.'%')->get();
            if($parcelFromOtherList){
                return view('Receives.receive_parcel_for_other_dc',compact(['parcelFromOtherList','employee']));
            }
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล กรุณาเข้าสู่ระบบใหม่อีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
                   
    public function getParcelDetailListFromOtherDC($id = null) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $emp_branch_id = $employee->emp_branch_id;
        // dd($emp_branch_id);
        $parcelDetailList = TransferDropCenter::where('transfer_bill_id_ref',$id)->get();
        if($parcelDetailList){
            $parcelBillId = $id;
            $ReciveTranferDropCenterDuplicates = ReciveTranferDropCenterDuplicate::where('duplicate_transfer_dropcenter_id', $emp_branch_id)->where('duplicate_bill_id', $id)->orderby('id', 'Desc')->get();

            $parcelReceiveDonelList = TransferDropCenter::where('transfer_bill_id_ref',$id)
            ->where('transfer_dropcenter_status',"ReceiveDone")
            ->orwhere('transfer_bill_id_ref',$id)
            ->where('transfer_dropcenter_status',"ReceiveDoneReturn")
            ->get();

            $parcelWrongList= TransferDropCenter::where('transfer_bill_id_ref',$id)
            ->where('transfer_dropcenter_status',"ParcelWrong")->get();

            $transferBillStatus = TransferDropCenterBill::where('id', $id)->first();

            return view('Receives.create_receive_parcel_for_other_dc',compact(["parcelDetailList","parcelBillId","parcelReceiveDonelList","parcelWrongList","transferBillStatus",'emp_branch_id','employee','ReciveTranferDropCenterDuplicates']));
        }
    }

    public function checkSendingStatusParcel(Request $request, $id) {
        $length = strlen($request->tracking_no);
        $substr_tracking_no = substr($request->tracking_no, 0,15);
        $substr_subtracking_no = substr($request->tracking_no, 15,$length);
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $emp_branch_id = $employee->emp_branch_id;
        if($id){
            $parcelDetailList = TransferDropCenter::where('transfer_bill_id_ref',$id)
            ->where('transfer_dropcenter_tracking_no',$substr_tracking_no)
            ->get();
            
            if(count($parcelDetailList)>0){
                if(count($parcelDetailList)>1){ //มีรายการซ้ำเกินมา อาจเกิดจากความผิดพลาดตอนสร้างลิสต์
                    ReciveTranferDropCenterDuplicate::create([
                        'duplicate_tracking_no' => $substr_tracking_no, 
                        'duplicate_transfer_dropcenter_id' => $employee->emp_branch_id,
                        'duplicate_bill_id' => $id,
                        'duplicate_status' => 2
                    ]);
                    return redirect()->back();
                }else{
                    foreach ($parcelDetailList as $transfer) {                        
                        if($transfer->transfer_dropcenter_status == "TransferToDropCenter" || $transfer->transfer_dropcenter_status == "TransferToDropCenterReturn"){
                            if($transfer->transfer_dropcenter_status == "TransferToDropCenter"){
                                $checkhave = explode(",", $transfer->parcel_received_amount);
                                $boxhavestatus = 0;
                                for ($boxi=0; $boxi < count($checkhave); $boxi++) { 
                                    if ($checkhave[$boxi] == $substr_subtracking_no) {
                                        $boxhavestatus = 1;
                                    }
                                }
                                
                                if($boxhavestatus == 1){
                                    if ($transfer->to_dc_received_amount == null) {
                                        $transfer->update([
                                            'to_dc_received_amount' => $substr_subtracking_no
                                        ]);
                                    }else{
                                        $subtrackingarray = explode(",", $transfer->to_dc_received_amount);
                                        $arrrayDuplicate = 0;
                                        for($i = 0; $i < count($subtrackingarray); $i++){
                                            if($substr_subtracking_no == $subtrackingarray[$i]){
                                                $arrrayDuplicate = 1;
                                            }
                                        }

                                        if($arrrayDuplicate == 0){
                                            $transfer->update([
                                                'to_dc_received_amount' => $transfer->to_dc_received_amount.','.$substr_subtracking_no
                                            ]);
                                        }else{
                                            ReciveTranferDropCenterDuplicate::create([
                                                'duplicate_tracking_no' => $substr_tracking_no, 
                                                'duplicate_transfer_dropcenter_id' => $employee->emp_branch_id,
                                                'duplicate_bill_id' => $id,
                                                'duplicate_status' => 3
                                            ]);
                                        }
                                    }

                                    $TransferDropCenter = TransferDropCenter::where('transfer_bill_id_ref',$id)->where('transfer_dropcenter_tracking_no',$substr_tracking_no)->first();
                                    $subtrackingarray = explode(",", $TransferDropCenter->to_dc_received_amount);
                                    // dd(count($subtrackingarray));
                                    if(count($subtrackingarray) == $TransferDropCenter->parcel_amount){
                                        $transfer->update([
                                            'transfer_dropcenter_status' => 'ReceiveDone'
                                        ]);

                                        $PacelCare = PacelCare::create([
                                            'tracking_id' => $TransferDropCenter->transfer_dropcenter_tracking_id, 
                                            'doing_by' => $employee->id,
                                            'branch_id' => $employee->emp_branch_id, 
                                            'status' => 4, 
                                            'ref_no' => $transfer->id
                                        ]);
                                    }
                                    
                                }else{
                                    ReciveTranferDropCenterDuplicate::create([
                                        'duplicate_tracking_no' => $substr_tracking_no, 
                                        'duplicate_transfer_dropcenter_id' => $employee->emp_branch_id,
                                        'duplicate_bill_id' => $id,
                                        'duplicate_status' => 1
                                    ]);
                                }
                                return redirect()->back();
                            }else if($transfer->transfer_dropcenter_status == "TransferToDropCenterReturn"){
                                $checkhave = explode(",", $transfer->parcel_received_amount);
                                $boxhavestatus = 0;
                                for ($boxi=0; $boxi < count($checkhave); $boxi++) { 
                                    if ($checkhave[$boxi] == $substr_subtracking_no) {
                                        $boxhavestatus = 1;
                                    }
                                }
                                
                                if($boxhavestatus == 1){
                                    if ($transfer->to_dc_received_amount == null) {
                                        $transfer->update([
                                            'to_dc_received_amount' => $substr_subtracking_no
                                        ]);
                                    }else{
                                        $subtrackingarray = explode(",", $transfer->to_dc_received_amount);
                                        $arrrayDuplicate = 0;
                                        for($i = 0; $i < count($subtrackingarray); $i++){
                                            if($substr_subtracking_no == $subtrackingarray[$i]){
                                                $arrrayDuplicate = 1;
                                            }
                                        }

                                        if($arrrayDuplicate == 0){
                                            $transfer->update([
                                                'to_dc_received_amount' => $transfer->to_dc_received_amount.','.$substr_subtracking_no
                                            ]);
                                        }else{
                                            ReciveTranferDropCenterDuplicate::create([
                                                'duplicate_tracking_no' => $substr_tracking_no, 
                                                'duplicate_transfer_dropcenter_id' => $employee->emp_branch_id,
                                                'duplicate_bill_id' => $id,
                                                'duplicate_status' => 3
                                            ]);
                                        }
                                    }

                                    $TransferDropCenter = TransferDropCenter::where('transfer_bill_id_ref',$id)->where('transfer_dropcenter_tracking_no',$substr_tracking_no)->first();
                                    $subtrackingarray = explode(",", $TransferDropCenter->to_dc_received_amount);
                                    // dd(count($subtrackingarray));
                                    if(count($subtrackingarray) == $TransferDropCenter->parcel_amount){
                                        $transfer->update([
                                            'transfer_dropcenter_status' => 'ReceiveDoneReturn'
                                        ]);

                                        $PacelCare = PacelCare::create([
                                            'tracking_id' => $TransferDropCenter->transfer_dropcenter_tracking_id, 
                                            'doing_by' => $employee->id,
                                            'branch_id' => $employee->emp_branch_id, 
                                            'status' => 15, 
                                            'ref_no' => $transfer->id
                                        ]);
                                    }
                                    
                                }else{
                                    ReciveTranferDropCenterDuplicate::create([
                                        'duplicate_tracking_no' => $substr_tracking_no, 
                                        'duplicate_transfer_dropcenter_id' => $employee->emp_branch_id,
                                        'duplicate_bill_id' => $id,
                                        'duplicate_status' => 1
                                    ]);
                                }
                                return redirect()->back();
                            }
                        }else if($transfer->transfer_dropcenter_status == "ReceiveDone"){
                            ReciveTranferDropCenterDuplicate::create([
                                'duplicate_tracking_no' => $substr_tracking_no, 
                                'duplicate_transfer_dropcenter_id' => $employee->emp_branch_id,
                                'duplicate_bill_id' => $id,
                                'duplicate_status' => 3
                            ]);
                            return redirect()->back();
                        }else{
                            ReciveTranferDropCenterDuplicate::create([
                                'duplicate_tracking_no' => $substr_tracking_no, 
                                'duplicate_transfer_dropcenter_id' => $employee->emp_branch_id,
                                'duplicate_bill_id' => $id,
                                'duplicate_status' => 4
                            ]);
                            alert()->error('ขออภัย', 'ขออภัย')->showConfirmButton('ตกลง', '#3085d6');
                            return redirect()->back();
                        }
                    }
                }
            }else{
                ReciveTranferDropCenterDuplicate::create([
                    'duplicate_tracking_no' => $substr_tracking_no, 
                    'duplicate_transfer_dropcenter_id' => $employee->emp_branch_id,
                    'duplicate_bill_id' => $id,
                    'duplicate_status' => 1
                ]);
                return redirect()->back();
            }
        }else{
            ReciveTranferDropCenterDuplicate::create([
                'duplicate_tracking_no' => $substr_tracking_no, 
                'duplicate_transfer_dropcenter_id' => $employee->emp_branch_id,
                'duplicate_bill_id' => $id,
                'duplicate_status' => 1
            ]);
            return redirect()->back();
        }
    }

    public function saveStatusDoneToTransferBill($id = null) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($id){
            $transferList = TransferDropCenter::where('transfer_bill_id_ref',$id)
            ->where('transfer_dropcenter_status',"TransferToDropCenter")
            ->orwhere('transfer_bill_id_ref',$id)
            ->where('transfer_dropcenter_status',"TransferToDropCenterReturn") // หารายการที่ยังไม่ได้ยืนยันรับ
            ->get();
            if(count($transferList) > 0){ //ถ้าเจอ
                alert()->error('ขออภัย', 'ยังมีรายการพัสดุที่ไม่ได้ยืนยันรับเข้า กรุณาตรวจสอบรายการให้ครบก่อน')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->back();
            }else{ //ถ้าไม่เจอ
                $updateStatusDoneToBill = TransferDropCenterBill::find($id);
                $transferallInBills = TransferDropCenter::where('transfer_bill_id_ref',$updateStatusDoneToBill->id)->get();
                $date = date('Y-m-d H:i:s');
                foreach($transferallInBills as $transferallInBill){
                    $Trackings = Tracking::where('tracking_no', $transferallInBill->transfer_dropcenter_tracking_no)->first();
                    if($Trackings->tracking_status == 'TransferToDropCenter'){
                        $Trackings->update([
                            'tracking_status' => 'ReceiveDone',
                            'orther_dc_revice_time' => $date
                        ]);
    
                        $Trackings = Tracking::where('tracking_no', $transferallInBill->transfer_dropcenter_tracking_no)->first();
                        $TrackingsLogs = TrackingsLog::create([
                            'tracking_no' => $transferallInBill->transfer_dropcenter_tracking_no, 
                            'tracking_receiver_id' => $Trackings->tracking_receiver_id,
                            'tracking_status_id' => 3, 
                            'tracking_branch_id_dc' => $transferallInBill->transfer_dropcenter_sender_id, 
                            'tracking_branch_id_sub_dc' => $transferallInBill->transfer_dropcenter_id,
                            'tracking_date' => $date
                        ]);
    
                        $PacelCare = PacelCare::create([
                            'tracking_id' => $Trackings->id, 
                            'doing_by' => $employee->id,
                            'branch_id' => $employee->emp_branch_id, 
                            'status' => 5, 
                            'ref_no' => $transferallInBill->id
                        ]);
                    }else if($Trackings->tracking_status == 'TransferToDropCenterReturn'){
                        $Trackings->update([
                            'tracking_status' => 'ReceiveDoneReturn',
                            'orther_dc_revice_time' => $date
                        ]);
    
                        $Trackings = Tracking::where('tracking_no', $transferallInBill->transfer_dropcenter_tracking_no)->first();
                        $TrackingsLogs = TrackingsLog::create([
                            'tracking_no' => $transferallInBill->transfer_dropcenter_tracking_no.'(RTN)', 
                            'tracking_receiver_id' => $Trackings->tracking_receiver_id,
                            'tracking_status_id' => 9, 
                            'tracking_branch_id_dc' => $transferallInBill->transfer_dropcenter_sender_id, 
                            'tracking_branch_id_sub_dc' => $transferallInBill->transfer_dropcenter_id,
                            'tracking_date' => $date
                        ]);
    
                        $PacelCare = PacelCare::create([
                            'tracking_id' => $Trackings->id,
                            'doing_by' => $employee->id,
                            'branch_id' => $employee->emp_branch_id, 
                            'status' => 16, 
                            'ref_no' => $transferallInBill->id
                        ]);
                    }
                }
                if($updateStatusDoneToBill){
                    $updateStatusDoneToBill->update([
                        'transfer_bill_status' => 'receive-done',
                        'tranfer_employee_recive_id' => $employee->id
                    ]);

                    $ReciveTranferDropCenterDuplicates = ReciveTranferDropCenterDuplicate::where('duplicate_bill_id', $id)->where('duplicate_transfer_dropcenter_id', $user->employee->emp_branch_id)->get();
                    foreach ($ReciveTranferDropCenterDuplicates as $ReciveTranferDropCenterDuplicate) {
                        $ReciveTranferDropCenterDuplicate->delete();
                    }

                    //TODO วิ่งไปเปลี่ยนสถานะพัสดุในคลังต้นทาง ให้เป็นคลังปลายทาง
                    alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
                    return redirect()->to('/getParcelListFromOtherDC/'.$updateStatusDoneToBill->transfer_recriver_id);
                }else{
                    alert()->error('ขออภัย', 'ไม่พบข้อมูล กรุณาลองใหม่อีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
                    return redirect()->back();
                }
            }
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล กรุณาลองใหม่อีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function closeJosbCurrier(Request $request) {
        $validator = Validator::make($request->all(), [
            'currier_id' => 'required'
        ]);
        if ($validator->fails()) {
            alert()->error('ขออภัย', 'กรอกข้อมูลไม่ครบ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
        $date = date('Y-m-d');
        $transfers = Transfer::where('transfer_courier_id',$request->currier_id)  //เลือกรายการที่ส่งสำเร็จของCRคนนั้น
        ->where('transfer_status','TransferToCourier')
        ->where('action_status','done')
        ->whereDate('created_at',$date)
        ->get();

        $transfer_cod_amount = 0;
        foreach($transfers as $transfer) {
            $subTrackings = SubTracking::where('subtracking_tracking_id',$transfer->transfer_tracking_id)->get();
            $subTracking_amount = 0;
            foreach ($subTrackings as $subTracking) {
                $subTracking_amount += $subTracking->subtracking_cod;
            }
            $transfer_cod_amount += $subTracking_amount;
        }

            if($transfers){
                foreach ($transfers as $transfer) {
                    $tracking = Tracking::find($transfer->transfer_tracking_id);
                    $tracking->update([
                        'tracking_status' => 'Success'
                    ]);

                    $subtrackings = SubTracking::where('subtracking_tracking_id', $tracking->id)->get();
                    foreach ($subtrackings as $subtracking) {
                        $subtracking->update([
                            'subtracking_status' => 'Success'
                        ]);
                    }
                }
                
                foreach ($transfers as $transfer) {
                    $transfer->update([
                        'transfer_status' => 'Success',
                        'action_status' => 'Success',
                        'curier_status' => 'Success'
                    ]);
                }
            }

            alert()->success('สำเร็จ', 'บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
    }

    public function createActionSuccess($id = null) {  //$id = booking_id
        if($id) {
            $transfer = Transfer::where('transfer_booking_id',$id)->first();
            $transfer->update([
                'curier_status' => 'Success'
            ]);
            return redirect()->to('/getCurierList');

        }else{
            alert()->error('ขออภัย', 'ไม่พบ Booking Id, กรุณาตรวจสอบอีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function deleteParcelWhenTransferToCurrire(Request $request) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($request->tracking_id){
            
            $tracking = Tracking::where('id',$request->tracking_id)->first();
            if (strpos($tracking->tracking_status, 'Return') !== false) {
                $logstatus = '10';
                $PacelCare_status = 17;
                $track_status_revorst = 'ReceiveDoneReturn';
                $rtn_show = '(RTN)';
            }else{
                $logstatus = '5';
                $PacelCare_status = 6;
                $track_status_revorst = 'done';
                $rtn_show = '';
            }

            $TrackingsLog = TrackingsLog::where('tracking_no',$tracking->tracking_no.$rtn_show)->where('tracking_status_id',$logstatus)->orderby('tracking_date', 'desc')->first();
            $booking = Booking::find($tracking->tracking_booking_id);
            if(!empty($TrackingsLog)){
                $TrackingsLog->delete();
            }
            if($booking->booking_branch_id == $employee->emp_branch_id){
                $tracking->update([
                    'tracking_status' => $track_status_revorst
                ]);
            }else{
                $tracking->update([
                    'tracking_status' => 'ReceiveDone'
                ]);
            }

            $transfer = Transfer::where('transfer_tracking_id',$request->tracking_id)->where('transfer_status', '!=', 'CustomerResiveDone')->where('transfer_status', '!=', 'ReturnDone')->where('transfer_status', '!=', 'ReturnBackToDC')->first();
            $transfer->delete();
            
            $PacelCare = PacelCare::where('tracking_id',$request->tracking_id)->where('status', $PacelCare_status)->first();
            if(!empty($PacelCare)){
                $PacelCare->delete();
            }

            alert()->success('สำเร็จ', 'ลบรายการสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->to('/getTransferByCourier/'.$request->currier_id);
        }else{
            alert()->error('ขออภัย', 'ไม่พบ Tracking Id, กรุณาตรวจสอบอีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function deleteParcelWhenTransferToDropCenter(Request $request) {
        // return $request->all();
        if($request->tracking_id){
            $tracking = Tracking::where('id',$request->tracking_id)->first();
            if($tracking->tracking_status == 'transferDCDoingReturn'){
                $tracking->update([
                    'tracking_status' => 'ReturnBack'
                ]);
    
                $transfer = TransferDropCenter::where('id',$request->transfer_id)->first();
                $transfer->delete();
                
                $PacelCare = PacelCare::where('tracking_id',$tracking->id)->where('status', '13')->first();
                $PacelCare->delete();
            }else{
                $tracking->update([
                    'tracking_status' => 'done'
                ]);
    
                $transfer = TransferDropCenter::where('id',$request->transfer_id)->first();
                $transfer->delete();
                
                $PacelCare = PacelCare::where('tracking_id',$tracking->id)->where('status', '2')->first();
                $PacelCare->delete();
            }
            
            alert()->success('สำเร็จ', 'ลบรายการสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->to('/getTransferByDropCenter/'.$request->receive_dc_id);
        }else{
            alert()->error('ขออภัย', 'ไม่พบ Tracking Id, กรุณาตรวจสอบอีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function printDeleveryReport($id = null) {
        $user = Auth::user();
        if($id){
            $date = date("Y-m-d");
            $TranserBill = TranserBill::find($id);
            $currier = Employee::where('id',$TranserBill->transfer_bill_courier_id)->first();
            // dd($currier);
            $dropCenter = DropCenter::where('id',$currier->emp_branch_id)->first();
            $transfers = Transfer::where('transfer_bill_id',$id)->get();
            if($transfers){
                $pdf = PDF::loadView('/Transfers.delivery_report',compact(['currier','transfers','dropCenter','user','TranserBill']));
                return $pdf->stream();
            }
        }else{
            alert()->error('ขออภัย', 'กรุณาตรวจสอบอีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function Courier_cod_closing($id = null) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        return view('Transfers.tranfer_bill',compact(['id','employee']));
    }
    
    public function getTranferBillListDatatable(Request $request) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        // dd($employee->emp_branch_id);
        if($employee->emp_branch_id !== ""){
            $TranserBills = TranserBill::where('tranfer_bill_branch_id',$request->id)
                                        ->whereDate('created_at', DB::raw('CURDATE()'))
                                        ->orwhere('tranfer_bill_branch_id',$request->id)
                                        ->where('transfer_bill_status', '!=', 'done')
                                        ->orderby('transfer_bill_status','Desc')
                                        ->get();
        }else{
            $TranserBills = TranserBill::whereDate('created_at', DB::raw('CURDATE()'))
                                        ->orwhere('transfer_bill_status', '!=', 'done')
                                        ->orderby('transfer_bill_status','Desc')
                                        ->get();
        }
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
            $btn .= '&nbsp;';
            $Transfers = Transfer::where('transfer_bill_id',$row->id)->where('transfer_status', 'CustomerResiveDone')->orwhere('transfer_bill_id',$row->id)->where('transfer_status', 'CustomerResiveDoneReturn')->get();
            $btnstatus = 1;
            $COD = 0;
            foreach ($Transfers as $Transfer) {
                if($Transfer->transfer_status == 'TransferToCourier'){
                    $btnstatus = 0;
                }
                if(strpos($Transfer->transfer_status, 'Return') !== false){
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $Transfer->transfer_tracking_id)->get();
                    foreach ($SubTrackings as $SubTracking) {
                        $COD += $SubTracking->subtracking_price;;
                    }
                }else{
                    $COD += $Transfer->cod_amount;
                }
                // $COD += $Transfer->cod_amount;
            }
            if($employee->emp_position !== 'พนักงานจัดส่งพัสดุ(Courier)' && $employee->emp_position !== 'พนักงานส่งพัสดุ(Line Haul)' ){
                if($btnstatus == 1){
                    if($row->transfer_bill_status == 'done'){
                        $btn .= '<a href="#" class="btn btn-success btn-sm" onclick="closingDetail(\''.$row->employee_closing->emp_firstname.'\',\''.$row->employee_closing->emp_lastname.'\',\''.$row->employee_closing->emp_position.'\',\''.$row->updated_at.'\')"><i class="metismenu-icon pe-7s-check"></i></a>';
                    }else{
                        if($row->transfer_bill_status == 'sendingCOD'){
                            $btn .= '<a href="#" class="btn btn-primary btn-sm" onclick="CODReciveConfirm(\''.number_format($COD,2).'\',\''.$row->id.'\')"><i class="metismenu-icon pe-7s-server"></i></a>';
                        }else if($employee->emp_position == 'ผู้จัดการสาขา(Drop Center Manager)' || $employee->emp_position == 'เจ้าของกิจการ(Owner)' || $employee->emp_position == 'ผู้จัดการเขตพื้นที่(Area Manager)'){
                            $Transfers = Transfer::where('transfer_bill_id', $row->id)->where('transfer_status', 'TransferToCourier')->get();
                            if(count($Transfers) > 0){
                                $btn .= '<button class="btn btn-secondary btn-sm" disabled><i class="metismenu-icon pe-7s-server"></i></button>';
                            }else{
                                $btn .= '<a href="#" class="btn btn-danger btn-sm" onclick="CODReciveConfirm(\''.number_format($COD,2).'\',\''.$row->id.'\')"><i class="metismenu-icon pe-7s-server"></i></a>';
                            }
                        }else{
                            $btn .= '<button class="btn btn-secondary btn-sm" disabled><i class="metismenu-icon pe-7s-server"></i></button>';
                        }
                    }
                }else{
                    $btn .= '<button class="btn btn-secondary btn-sm" disabled><i class="metismenu-icon pe-7s-server"></i></button>';
                }
            }
            return $btn;
        })
        ->rawColumns(['action' => 'action'])
        ->make(true);
    }

    public function Recive_cod($id = null){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $TranserBill = TranserBill::find($id);
        $TranserBill->update([
            'transfer_bill_status' => 'done',
            'tranfer_closing_by_employee_id' => $employee->id
        ]);

        alert()->success('รับยอดสำเร็จ', 'ปิดยอดใบนำส่งสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
        return redirect()->back();
    }
   
    public function Tranfer_tracking_list($id = null){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        // $ReturnParcels = ReturnParcel::where('to_drop_center_id',$id)->where('return_status', 'new')->orwhere('to_drop_center_id',$id)->where('return_status', 'newReturn')->get();
        // $sql = "SELECT * FROM transfers b WHERE b.transfer_bill_id in (SELECT a.id FROM transer_bills a WHERE a.tranfer_bill_branch_id = '$id' AND a.transfer_bill_status = 'sendingCOD' OR a.tranfer_bill_branch_id = '$id' AND a.transfer_bill_status = 'TransferToCourier')";
        // $tranfer_tracking_lists = DB::select($sql);

        return view('Transfers.Courier_tracking_sending',compact(['id','employee','ReturnParcels']));
    }
    public function Tranfer_call_return($id = null){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $ReturnParcels = ReturnParcel::where('to_drop_center_id',$id)->where('return_status', 'new')->orwhere('to_drop_center_id',$id)->where('return_status', 'newReturn')->get();
        // $sql = "SELECT * FROM transfers b WHERE b.transfer_bill_id in (SELECT a.id FROM transer_bills a WHERE a.tranfer_bill_branch_id = '$id' AND a.transfer_bill_status = 'sendingCOD' OR a.tranfer_bill_branch_id = '$id' AND a.transfer_bill_status = 'TransferToCourier')";
        // $tranfer_tracking_lists = DB::select($sql);

        // dd("sss");
        return view('Transfers.Tranfer_call_return',compact(['id','employee','ReturnParcels']));
    }
    
    public function Tranfer_pod_closing($id = null, $tracking_no = null){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();

        if($tracking_no != null){
            $tracking_no = substr($tracking_no, 0,15);
        }

        $Tracking = Tracking::where('tracking_no', $tracking_no)->where('tracking_status', 'done')
        ->orwhere('tracking_no', $tracking_no)->where('tracking_status', 'ReceiveDoneReturn')
        ->orwhere('tracking_no', $tracking_no)->where('tracking_status', 'ReceiveDone')
        ->orwhere('tracking_no', $tracking_no)->where('tracking_status', 'ReturnBack')
        ->orderby('created_at', 'desc')->first();
        if(!empty($Tracking)){
            $SubTrackings = SubTracking::where('subtracking_tracking_id', $Tracking->id)->orderby('subtracking_under_tracking_id', 'asc')->get();
        }else{
            $SubTrackings[] = "";
            if($tracking_no != null){
                alert()->error('ไม่พบหมายเลข Tracking', 'ปิดยอดใบนำส่งสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->to('/Tranfer_pod_closing/'.$id);
            }
        }

        return view('Transfers.Tranfer_pod_closing',compact(['id','employee','Tracking','SubTrackings']));
    }
    
    public function find_detail_for_closing(Request $request){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();

        return view('Transfers.Tranfer_pod_closing',compact(['id','employee']));
    }
    
    public function pod_closing_form_submit(Request $request){
        $validator = Validator::make($request->all(), [
            'transfer_booking_id' => 'required',
            'transfer_status' => 'required',
            'transfer_tracking_id' => 'required',
            'parcel_amount' => 'required',
            'photo' => 'required',
            'receive_name' => 'required',
            'receive_relation' => 'required',
            'close_type' => 'required',
            'money_amount' => 'required'
        ]);
        if ($validator->fails()) {
            alert()->error('ขออภัย', 'กรอกข้อมูลไม่ครบ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
        if($request->receive_relation == '0'){
            $request->receive_relation = $request->receive_relation_orther;
        }
        // dd($request->photo);
        $photo = str_replace("data:image/jpeg;base64,","",$request->photo);
        // dd($photo);
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $Tracking = Tracking::where('id', $request->transfer_tracking_id)->where('tracking_status', 'done')
        ->orwhere('id', $request->transfer_tracking_id)->where('tracking_status', 'ReceiveDoneReturn')
        ->orwhere('id', $request->transfer_tracking_id)->where('tracking_status', 'ReceiveDone')
        ->orwhere('id', $request->transfer_tracking_id)->where('tracking_status', 'ReturnBack')
        ->orderby('created_at', 'desc')->first();
        if($Tracking){
            if($Tracking->tracking_status == "ReturnBack"){
                alert()->error('ขออภัย', 'กรุณายกเลิกสถานะการส่งกลับก่อน')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->back();
            }else{
                if($request->close_type == '1'){

                    $cod_amount = $request->money_amount;
                    $tracking_status = "CustomerResiveDone";
                    $rtn_show = "";
                    $care_status = 9;
                    $log_status = 7;

                }else if($request->close_type == '2'){
                    
                    $cod_amount = 0;
                    $tracking_status = "CustomerResiveDoneReturn";
                    $rtn_show = "(RTN)";
                    $care_status = 20;
                    $log_status = 13;

                }
                $Transfer = Transfer::create([
                    'transfer_booking_id' => $request->transfer_booking_id,
                    'transfer_courier_id' => $employee->id,
                    'transfer_status' => 'CustomerResiveDone',
                    'transfer_tracking_id' => $request->transfer_tracking_id,
                    'parcel_amount' => $request->parcel_amount,
                    'photo' => $photo,
                    'receive_name' => $request->receive_name,
                    'receive_relation' => $request->receive_relation,
                    'cod_amount' => $cod_amount,
                    'transfer_branch_id' => $employee->emp_branch_id
                ]);

                $Tracking->update([
                    'tracking_status' => $tracking_status
                ]);
                
                $date = date('Y-m-d H:i:s');
                $TrackingsLogs = TrackingsLog::create([
                    'tracking_no' => $Tracking->tracking_no.$rtn_show, 
                    'tracking_receiver_id' => $Tracking->tracking_receiver_id,
                    'tracking_status_id' => $log_status, 
                    'tracking_branch_id_dc' => $Tracking->booking->booking_branch_id, 
                    'tracking_branch_id_sub_dc' => $Tracking->customer->PostCode->drop_center_id,
                    'tracking_date' => $date
                ]);
                
                $date = date('Y-m-d');
                $PacelCare = PacelCare::where('tracking_id', $Tracking->id)->where('status', '9')->where('created_at','like', $date.'%')->first();
                if(empty($PacelCare)){
                    $PacelCare = PacelCare::create([
                        'tracking_id' => $Tracking->id, 
                        'doing_by' => $employee->id,
                        'branch_id' => $employee->emp_branch_id, 
                        'status' => $care_status, 
                        'ref_no' => '',
                    ]);
                }
                alert()->success('สำเร็จ', 'ปิดPODพัสดุหมายเลข '.$Tracking->tracking_no.' เรียบร้อย')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->to('/Tranfer_pod_closing/'.$employee->emp_branch_id);
            }
        }else{
            alert()->error('ขออภัย', 'กรอกข้อมูลไม่ครบ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }

    }
    
    public function tranfer_sending_list(Request $request){
        $sql = "SELECT c.tranfer_by_employee_id, b.* FROM transfers b LEFT JOIN transer_bills c ON c.id = b.transfer_bill_id WHERE b.transfer_bill_id in (SELECT a.id FROM transer_bills a WHERE a.tranfer_bill_branch_id = '$request->id' AND a.transfer_bill_status = 'sendingCOD' OR a.tranfer_bill_branch_id = '$request->id' AND a.transfer_bill_status = 'TransferToCourier')";
        $tranfer_tracking_lists = DB::select($sql);

        return Datatables::of($tranfer_tracking_lists)
        ->addIndexColumn()
        ->editColumn('transfer_tracking_id',function($row){
            $Tracking = Tracking::find($row->transfer_tracking_id);
            $rtnshow = "";
            if(strpos($row->transfer_status, 'Return') !== false && $row->transfer_status !== 'ReturnBackToDC'){
                $rtnshow = "(RTN)";
            }
            return $Tracking->tracking_no.$rtnshow;
        })
        ->editColumn('cust_name',function($row){
            $Tracking = Tracking::find($row->transfer_tracking_id);
            return $Tracking->receiver->cust_name;
        })
        ->editColumn('cust_send_name',function($row){
            $Tracking = Tracking::find($row->transfer_tracking_id);
            $Booking = Booking::find($Tracking->tracking_booking_id);
            return $Booking->customer->cust_name;
        })
        ->editColumn('subtracking_cod', function($row){
            $Tracking = Tracking::find($row->transfer_tracking_id);
            $SubTrackings = SubTracking::where('subtracking_tracking_id', $Tracking->id)->get();
            $cod = 0;
            foreach ($SubTrackings as $SubTracking) {
                $cod += $SubTracking->subtracking_cod;
            }
            return number_format($cod,2);
        })
        ->editColumn('courier_sending', function($row){
            $transfer = transfer::find($row->id);
            return $transfer->employee->emp_firstname.' '.$transfer->employee->emp_lastname;
        })
        ->editColumn('created_at', function($row){
            $date = substr($row->created_at, 8,2).'/';
            $date .= substr($row->created_at, 5,2).'/';
            $date .= substr($row->created_at, 0,4).'<br>';
            $date .= substr($row->created_at, 11,5);
            return $date;
        })
        ->editColumn('tranfer_by_employee_id', function($row){
            $Employee = Employee::find($row->tranfer_by_employee_id);
            // dd($Employee);
            return $Employee->emp_firstname.' '.$Employee->emp_lastname;
        })
        ->addColumn('action', function($row){
            if($row->transfer_status == 'TransferToCourier' || $row->transfer_status == 'TransferToCourierReturn'){
                $call_zero = 0;
                $call_one = 0;
                $call_two = 0;
                $refuse = 0;
                $date = date('Y-m-d');
                $i = 0;
                $last_status = 0;
                $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE tranfer_id = '$row->id' AND tracking_id = '$row->transfer_tracking_id' AND courier_id = '$row->transfer_courier_id' AND created_at like '$date%' order by created_at DESC";
                $courier_call_lists = DB::select($sql);
                foreach ($courier_call_lists as $courier_call_list) {
                    $i++;
                    if($i == 1){
                        $last_note = $courier_call_list->note;
                        $last_status = $courier_call_list->callstatus;
                    }
                    if($courier_call_list->note == 'ปฏิเสธ รับพัสดุ'){
                        if($refuse == 0){
                            $refuse = 1;
                        }
                    }else if($courier_call_list->note == 'เบอร์ผิด'){
                        if($refuse == 0){
                            $refuse = 2;
                        }
                    }
                    if($courier_call_list->callstatus == 0){
                        $call_zero += 1;
                    }else if($courier_call_list->callstatus == 1){
                        $call_one += 1;
                    }else if($courier_call_list->callstatus == 2){
                        $call_two += 1;
                    }
                }
                if($call_zero >= 1){
                    $content = "พัสดุรอนำส่ง";
                    $color_status = "outline-primary";
                }else if($call_one >= 1){
                    $content = "ผู้รับเลื่อนรับ";
                    $color_status = "outline-danger";
                }else if($call_two >= 3){
                    $content = "พัสดุติดปัญหา";
                    $color_status = "outline-danger";
                }else if($call_two >= 1){
                    $content = "กำลังติดต่อผู้รับ";
                    $color_status = "outline-primary";
                }else{
                    $content = "นำส่งตามลำดับ";
                    $color_status = "outline-secondary";
                }

                if($last_status !== 1){
                    if($refuse == 1){
                        $content = $last_note;
                        $color_status = "outline-danger";
                    }else if($refuse == 2){
                        $content = $last_note;
                        $color_status = "outline-danger";
                    }
                }
            }else if($row->transfer_status == 'CustomerResiveDone' || $row->transfer_status == 'CustomerResiveDoneReturn'){
                $content = "จัดส่งสำเร็จ";
                $color_status = "success";
            }else if($row->transfer_status == 'ReturnBackToDC'){
                $content = "รับคืนDCแล้ว";
                $color_status = "danger";
            }
            return '<button class="btn btn-'.$color_status.'" style="width:100%;" onclick="viewDetail(\''.$row->id.'\',\''.$row->transfer_tracking_id.'\',\''.$row->transfer_courier_id.'\',\''.$color_status.'\',\''.$content.'\')">'.$content.'</button>';
        })
        ->rawColumns(['action' => 'action','created_at' => 'created_at'])
        ->make(true);
    }

    public function getfind_call_history(Request $request){
        // dd($request->all());
        $date = date('Y-m-d');
        $CourierCalls = CourierCall::where('tranfer_id', $request->id)->where('tracking_id', $request->track_id)->where('courier_id', $request->courier_id)->where('created_at', 'like', $date.'%')->orderby('created_at', 'desc')->get();

        return json_decode($CourierCalls);
    }
    
    public function getfind_call_history_recive(Request $request){
        $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE request_service_id = '$request->id' AND courier_id = '$request->courier_id' order by created_at asc";
        $courier_call_lists = DB::select($sql);
        $call_status = json_encode($courier_call_lists, JSON_UNESCAPED_UNICODE);

        return $call_status;
    }
    
    public function return_parcel(Request $request){
        // dd($request->all());
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $length = strlen($request->tracking_no);
        if ($length >= 16) {
            $substr_tracking_no = substr($request->tracking_no, 0,15);
            $substr_subtracking_no = substr($request->tracking_no, 15,$length);
            $Tracking = Tracking::where('tracking_no', $substr_tracking_no)->first();
            if(!empty($Tracking)){
                $Transfer = Transfer::where('transfer_tracking_id', $Tracking->id)->where('transfer_status', 'TransferToCourier')->where('transfer_branch_id', $employee->emp_branch_id)->orwhere('transfer_tracking_id', $Tracking->id)->where('transfer_status', 'TransferToCourierReturn')->where('transfer_branch_id', $employee->emp_branch_id)->first();
                if(!empty($Transfer)){
                    $boxhave = 0;
                    $parcel_box_nos = explode(",", $Transfer->parcel_received_amount);
                    for($i = 0; $i < count($parcel_box_nos); $i++){
                        if($substr_subtracking_no == $parcel_box_nos[$i]){
                            $boxhave = 1;
                        }
                    }
                    
                    $rtnshow = "";
                    $tracking_Log_status = 4;
                    $PacelCare_status = 10;
                    if(strpos($Transfer->transfer_status, 'Return') !== false){
                        $rtnshow = "Return";
                        $tracking_Log_status = 11;
                        $PacelCare_status = 21;
                    }
                    
                    if($boxhave == '1'){
                        $ReturnParcel = ReturnParcel::where('return_tracking_id', $Transfer->transfer_tracking_id)->where('return_status', 'new')->orwhere('return_tracking_id', $Transfer->transfer_tracking_id)->where('return_status', 'newReturn')->first();
                        if(!empty($ReturnParcel)){
                            $boxreceive = 1;
                            $ReturnParcel_box_nos = explode(",", $ReturnParcel->parcel_receive);
                            for($ibobreceive = 0; $ibobreceive < count($ReturnParcel_box_nos); $ibobreceive++){
                                if ($substr_subtracking_no == $ReturnParcel_box_nos[$ibobreceive]) {
                                    $boxreceive = 0;
                                }
                            }

                            if($boxreceive == '1'){
                                $ReturnParcel->update([
                                    'parcel_receive' => $ReturnParcel->parcel_receive.','.$substr_subtracking_no,
                                ]);
                            }else{
                                alert()->error('ขออภัย', 'รายการยิงซ้ำ')->showConfirmButton('ตกลง', '#3085d6');
                            }
                        }else{
                            $ReturnParcel = ReturnParcel::create([
                                'return_tracking_id' => $Transfer->transfer_tracking_id, 
                                'tracking_no' => $substr_tracking_no, 
                                'parcel_receive' => $substr_subtracking_no,
                                'parcel_amount' => $Transfer->parcel_amount,
                                'from_courier_id' => $Transfer->transfer_courier_id,
                                'to_drop_center_id' => $request->branch_id,
                                'return_doing_by_employee_id' => $employee->id,
                                'tranfer_bill_id' => $Transfer->transfer_bill_id,
                                'return_status' => 'new'.$rtnshow
                            ]);

                            $PacelCare = PacelCare::create([
                                'tracking_id' => $Transfer->transfer_tracking_id, 
                                'doing_by' => $employee->id,
                                'branch_id' => $employee->emp_branch_id, 
                                'status' => $PacelCare_status, 
                                'ref_no' => $ReturnParcel->id
                            ]);
                        }
                    }else{
                        alert()->error('ขออภัย', 'หมายเลขกล่องไม่ถูกต้อง')->showConfirmButton('ตกลง', '#3085d6');
                    }
                }else{
                    alert()->error('ขออภัย', 'รายการ Tracking ไม่อยู่ในสถานะรับกลับ หรือไม่ได้นำส่งจากสาขาของคุณ')->showConfirmButton('ตกลง', '#3085d6');
                }
            }else{
                alert()->error('ขออภัย', 'ไม่พบรายการ Tracking')->showConfirmButton('ตกลง', '#3085d6');
            }
        }else{
            alert()->error('ขออภัย', 'หมายเลข Tracking ไม่ถูกต้อง')->showConfirmButton('ตกลง', '#3085d6');
        }

        return redirect()->back();
    }

    public function return_Parcel_delete($id = null){
        if ($id != null) {
            $ReturnParcel = ReturnParcel::find($id);

            $PacelCare_status = 10;
            if(strpos($ReturnParcel->Tracking->tracking_status, 'Return') !== false){
                $PacelCare_status = 21;
            }

            $PacelCare = PacelCare::where('tracking_id',$ReturnParcel->return_tracking_id)->where('status', $PacelCare_status)->first();
            // dd($PacelCare);
            $PacelCare->delete();

            $ReturnParcel->delete();

        }else{
            alert()->error('ขออภัย', 'กรุณาลองใหม่อีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
        }
        return redirect()->back();
    }
    
    public function save_return_back_to_dc($id = null){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();

        $ReturnParcels = ReturnParcel::where('to_drop_center_id',$id)->where('return_status', 'new')->orwhere('to_drop_center_id',$id)->where('return_status', 'newReturn')->get();
        if(count($ReturnParcels) > 0){
            // dd($ReturnParcels);
            foreach ($ReturnParcels as $ReturnParcel) {
                $Tracking = Tracking::find($ReturnParcel->return_tracking_id);
                // dd($Tracking->tracking_receiver_id);
                $customerresive = Customer::find($Tracking->tracking_receiver_id);
                $PostCode = PostCode::where('postcode', $customerresive->cust_postcode)->first();
                $booking = Booking::find($Tracking->tracking_booking_id);
                $returnamount = $Tracking->parcel_return_amount+1;
                // dd($returnamount);

                $Transfer = Transfer::where('transfer_bill_id', $ReturnParcel->tranfer_bill_id)->where('transfer_tracking_id', $ReturnParcel->return_tracking_id)->where('transfer_status', 'TransferToCourier')->orwhere('transfer_bill_id', $ReturnParcel->tranfer_bill_id)->where('transfer_tracking_id', $ReturnParcel->return_tracking_id)->where('transfer_status', 'TransferToCourierReturn')->first();
                // dd($Transfer);
                $logrtn = "";
                $tracking_Log_status = 4;
                $PacelCare_status = 11;
                if($booking->booking_branch_id == $employee->emp_branch_id){
                    $rtnshow = "done";
                    if(strpos($Transfer->transfer_status, 'Return') !== false){
                        $rtnshow = "ReceiveDoneReturn";
                        $logrtn = "(RTN)";
                        $tracking_Log_status = 11;
                        $PacelCare_status = 22;
                    }
                    
                    $Transfer->update([
                        'transfer_status' => 'ReturnBackToDC',
                        'recive_admit' => NULL,
                    ]);
                    $Tracking->update([
                        'tracking_status' => $rtnshow,
                        'parcel_return_amount' => $returnamount
                    ]);
                    $ReturnParcel->update([
                        'return_status' => 'ReturnDone'
                    ]);
                }else{
                    $Transfer->update([
                        'transfer_status' => 'ReturnBackToDC'
                    ]);
                    $Tracking->update([
                        'tracking_status' => 'ReceiveDone',
                        'parcel_return_amount' => $returnamount
                    ]);
                    $ReturnParcel->update([
                        'return_status' => 'ReturnDone'
                    ]);
                }

                $date = date('Y-m-d H:i:s');
                $TrackingsLogs = TrackingsLog::create([
                    'tracking_no' => $Tracking->tracking_no.$logrtn, 
                    'tracking_receiver_id' => $Tracking->tracking_receiver_id,
                    'tracking_status_id' => $tracking_Log_status, 
                    'tracking_branch_id_dc' => $Tracking->booking->booking_branch_id, 
                    'tracking_branch_id_sub_dc' => $PostCode->drop_center_id,
                    'tracking_date' => $date
                ]);

                $PacelCare = PacelCare::create([
                    'tracking_id' => $Transfer->transfer_tracking_id, 
                    'doing_by' => $employee->id,
                    'branch_id' => $employee->emp_branch_id, 
                    'status' => $PacelCare_status, 
                    'ref_no' => $ReturnParcel->id
                ]);
            }

            alert()->success('สำเร็จ', 'เรียกคืนพัสดุสำเร็จแล้ว')->showConfirmButton('ตกลง', '#3085d6');
        }else{
            alert()->error('ไม่สำเร็จ', 'ไม่พบรายการเรียกคืน')->showConfirmButton('ตกลง', '#3085d6');
        }
        return redirect()->back();
    }

    public function find_transfer_bill(Request $request){
        $validator = Validator::make($request->all(), [
            'transfer_bill_no' => 'required'
        ]);
        if ($validator->fails()) {
            return 'required';
        }
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();

        $TransferDropCenterBill = TransferDropCenterBill::where('transfer_recriver_id', $employee->emp_branch_id)->where('transfer_bill_no', $request->transfer_bill_no)->first();
        if(!empty($TransferDropCenterBill)){
            return $TransferDropCenterBill->id;
        }else{
            return 'no_id';
        }

    }
}