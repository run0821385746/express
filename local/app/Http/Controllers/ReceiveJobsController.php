<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\ReceiveJob;
use App\Model\Booking;
use App\Model\Customer;
use App\Model\Tracking;
use App\Model\SubTracking;
use App\Model\Employee;
use App\Model\SaleOther;
use App\Model\ReciveRequest;
use App\Model\RequestService;
use App\Model\PacelCare;
use Validator;
use Auth;
use DB;

class ReceiveJobsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $request->all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


     public function receiveJobsList($id = null) {
        //  return "333333";
        $receiveJobs = ReceiveJob::where('booking_branch_id', $id)->get();
        return view('Receives/receive',compact('receiveJobs'));
     }


    public function show($id)
    {
        //  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getReceiveDetail($id = null) {
        if($id){
            $booking = Booking::find($id);
            $trackings = Tracking::where('tracking_booking_id',$booking->id)->where('tracking_no','not like', '%Destroy')->get();
            $sender = Customer::where('id',$booking->booking_sender_id)->first();
            $subTrackings = SubTracking::where('subtracking_booking_id',$booking->id)->get();
            // dd($subTrackings);
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $saleOtherList = SaleOther::where('sale_other_booking_id',$id)->get();
            return view('/Receives.receive_detail',compact(['booking','trackings','sender','subTrackings','employee','saleOtherList']));
        }else{
            alert()->error('ขออภัย', 'ไม่พบเลขที่Bookingที่ต้องการดูรายละเอียด กรุณาทำรายการใหม่')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function getReceive_bycourier_Detail($id = null) {
        if($id){
            $booking = Booking::find($id);
            $trackings = Tracking::where('tracking_booking_id',$booking->id)->get();
            $sender = Customer::where('id',$booking->booking_sender_id)->first();
            $subTrackings = SubTracking::where('subtracking_booking_id',$booking->id)->get();
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $saleOtherList = SaleOther::where('sale_other_booking_id',$id)->get();
            return view('/Receives.receive_detail_request',compact(['booking','trackings','sender','subTrackings','employee','saleOtherList']));
        }else{
            alert()->error('ขออภัย', 'ไม่พบเลขที่Bookingที่ต้องการดูรายละเอียด กรุณาทำรายการใหม่')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
    
    public function create_recive_from_request($id = null) {
        if($id){
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();

            $sql = "SELECT a.* FROM trackings a LEFT JOIN bookings b ON b.id = a.tracking_booking_id WHERE a.tracking_status = 'request-done' AND b.booking_branch_id = '$employee->emp_branch_id'";
            $track_unsuccessfull = DB::select($sql);
            
            $sql = "SELECT * FROM recive_requests WHERE recive_status = 'done' AND branch_id = '$employee->emp_branch_id' AND DATE(created_at) = CURDATE()";
            $track_success = DB::select($sql);
            
            $sql = "SELECT * FROM recive_requests WHERE recive_status = '' AND branch_id = '$employee->emp_branch_id' AND DATE(created_at) = CURDATE()";
            $track_doing = DB::select($sql);

            $ReciveRequests = ReciveRequest::where('branch_id', $employee->emp_branch_id)->where('recive_status', '')->get();
            return view('/Receives.create_recive_from_request',compact(['ReciveRequests','employee','track_unsuccessfull','track_success','track_doing']));
        }else{
            alert()->error('ขออภัย', 'เกิดข้อผิดพลาด กรุณาลองใหม่')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function add_recive_from_courier_request(Request $request){
        $validator = Validator::make($request->all(), [
            'tracking_no' => 'required'
        ]);

        if ($validator->fails()) {
            alert()->error('ขออภัย', 'กรุณากรอกหมายเลข Tracking')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $track_count = strlen($request->tracking_no);
        // dd($track_count);
        if($track_count >= 16){
            $trackking_no = substr($request->tracking_no, 0,15);
            $box_no = substr($request->tracking_no, 15,$track_count);
            // dd($box_no);
            $Tracking = Tracking::where('tracking_no', $trackking_no)->orderby('created_at', 'desc')->first();
            if(!empty($Tracking)){
                $ReciveRequest = ReciveRequest::where('tracking_id', $Tracking->id)->first();
                if(!empty($ReciveRequest)){
                    $SubTrackings_checkhave = SubTracking::where('subtracking_tracking_id', $Tracking->id)->where('subtracking_under_tracking_id', $box_no)->first();
                    if(!empty($SubTrackings_checkhave)){
                        $recive_checkarray = explode(",", $ReciveRequest->recive_check);
                        sort($recive_checkarray);
                        if (!in_array($box_no, $recive_checkarray)){
                            $ReciveRequest->update([
                                'recive_check' => $ReciveRequest->recive_check.','.$box_no
                            ]);
                            return redirect()->back();
                        }else{
                            alert()->error('ขออภัย', 'พัสดุชิ้นนี้ถูกทำรับแล้ว')->showConfirmButton('ตกลง', '#3085d6');
                            return redirect()->back();
                        }
                    }else{
                        alert()->error('ขออภัย', 'หมายเลขพัุสดุไม่ถูกต้อง')->showConfirmButton('ตกลง', '#3085d6');
                        return redirect()->back();
                    }
                }else{
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $Tracking->id)->get();
                    $SubTrackings_checkhave = SubTracking::where('subtracking_tracking_id', $Tracking->id)->where('subtracking_under_tracking_id', $box_no)->first();
                    if(!empty($SubTrackings_checkhave)){
                        $RequestService = RequestService::where('request_booking_id',$SubTrackings_checkhave->subtracking_booking_id)->where('branch_id', $employee->emp_branch_id)->first();
                        $ReciveRequest = ReciveRequest::create([
                            'tracking_id'  => $Tracking->id, 
                            'tracking_No'  => $Tracking->tracking_no, 
                            'parcel_amount'  => count($SubTrackings), 
                            'recive_check'  => $box_no,  
                            'recive_by'  => $employee->id,
                            'branch_id'  => $employee->emp_branch_id,
                            'from_courier'  => $RequestService->request_currier_id,  
                            'recive_status'  => '',  
                            'booking_id'  => $RequestService->request_booking_id
                        ]);
                        return redirect()->back();
                    }else{
                        alert()->error('ขออภัย', 'หมายเลขTrackingไม่ถูกต้อง')->showConfirmButton('ตกลง', '#3085d6');
                        return redirect()->back();
                    }
                }
            }else{
                alert()->error('ขออภัย', 'ไม่พบหมายเลขTracking')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->back();
            }
        }else{
            alert()->error('ขออภัย', 'หมายเลขTrackingไม่ถูกต้อง')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }

    }

    public function delete_recive_from_courier_request(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            alert()->error('ขออภัย', 'เกิดข้อผิดพลาด')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }

        $ReciveRequest = ReciveRequest::find($request->id);
        if(!empty($ReciveRequest)){
            $ReciveRequest->delete();
        }
        return redirect()->back();
    }
    
    public function save_recive_from_courier_request($id = null){

        if ($id == null) {
            alert()->error('ขออภัย', 'เกิดข้อผิดพลาด')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }

        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();

        $ReciveRequests_groupby = ReciveRequest::where('branch_id', $id)->where('recive_status', '')->groupby('booking_id')->get();
        $ReciveRequests = ReciveRequest::where('branch_id', $id)->where('recive_status', '')->get();
        foreach ($ReciveRequests as $key => $ReciveRequest) {
            $Tracking = Tracking::find($ReciveRequest->tracking_id);
            $Tracking->update([
                'tracking_status' => 'done'
            ]);

            $ReciveRequest->update([
                'recive_status' => 'done'
            ]);

            $PacelCare = PacelCare::create([
                'tracking_id' => $Tracking->id, 
                'doing_by' => $employee->id,
                'branch_id' => $employee->emp_branch_id, 
                'status' => 1, 
                'ref_no' => null
            ]);
        }

        foreach ($ReciveRequests_groupby as $key => $ReciveRequest) {
            $RequestService = RequestService::where('request_booking_id',$ReciveRequest->booking_id)->where('branch_id', $employee->emp_branch_id)->first();
            $Tracking = Tracking::where('tracking_booking_id', $RequestService->request_booking_id)->where('tracking_status', 'request-done')->get();
            if(count($Tracking) == 0){
                $RequestService->update([
                    'request_status' => 'done'
                ]);

                $booking = Booking::find($ReciveRequest->booking_id);
                $booking->update([
                    'booking_status' => 'done'
                ]);
            }
        }
        alert()->success('สำเร็จ', 'ทำรับสำเร็จแล้ว')->showConfirmButton('ตกลง', '#3085d6');
        return redirect()->back();
    }
}
