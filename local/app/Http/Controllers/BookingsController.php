<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Booking;
use App\Model\Employee;
use App\Model\Customer;
use App\Model\Tracking;
use App\Model\SubTracking;
use App\Model\TrackingsLog;
use App\Model\DropCenter;
use App\Model\PostCode;
use App\Model\PacelCare;
use App\Model\RequestService;
use Validator;
use Auth;
use DB;
use PDF;
use Dompdf\Dompdf;

class BookingsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


     //ใช้ตอนเลือกผู้ส่งพัสดุแล้วส่งค่ากลับมาหน้า input  เพื่อเอาข้อมูลผู้ส่งมาแสดงใน view
    public function store(Request $request)
    {
        // dd($request->all());
        $user = Auth::user();
        if($user){
            // dd("sss");
            if($request->booking_id){
                $booking = Booking::find($request->booking_id);
                if($booking){
                    if($booking->booking_type == "2" && $booking->booking_status == 'request'){
                        $RequestService = RequestService::where('request_booking_id', $booking->id)->where('request_status', 'request')->first();
                        if($RequestService){
                            $RequestService->update([
                                'request_status' => 'cancel',
                                'action_status' => $user->employee_id
                            ]);
                        }
                    }
                    $booking->update([
                        'booking_sender_id' => $request->customer_id,
                        'booking_type' => $request->booking_type
                    ]);
    
                    $booking_id = $booking->id;
                    $bookingData = Booking::where('id',$booking_id)->first();
                    $trackingList = Tracking::where('tracking_no', '!=', '')->where('tracking_no', 'NOT LIKE', '%Destroy')->where('tracking_booking_id',$booking_id)->get();
                    $customer = Customer::where('id',$request->customer_id)->first();
                    $currierList = Employee::where('emp_position', "พนักงานจัดส่งพัสดุ(Courier)")->where('emp_branch_id',$user->emp_branch_id)->where('emp_status',"1")->get();
                    $employee = Employee::where('id',$user->employee_id)->first();
                    return view('/input',compact(['bookingData','customer','trackingList','currierList','employee']));
                }else{
                    alert()->error('ขออภัย', 'ขออภัย กรุณาทำรายการใหม่')->showConfirmButton("ตกลง","#3085d6");
                    return redirect()->back();
                }
                
            }else{
                $validator = Validator::make($request->all(), [
                    'branch_id' => 'required',
                    'customer_id' => 'required',
                    'booking_type' => 'required',
                ]);
                if($validator->fails()) {
                    // dd("ddd");
                    alert()->error('ขออภัย', 'กรุณาเข้าสู่ระบบใหม่ ก่อนเปิดรายการรับพัสดุใหม่')->showConfirmButton("ตกลง","#3085d6");
                    return redirect()->back();
                } 
                // dd("dd");
                // generate receive document no
                $countRow = Booking::where('booking_branch_id',$request->branch_id)->get();
                $num_row = count($countRow);
                $digit = count($countRow)+11;
                // dd($digit);

                $num_row < 99999 ? $documentNo = "SE00000000".$digit : null;
                $num_row < 9999 ? $documentNo = "SE000000000".$digit : null;
                $num_row < 999 ? $documentNo = "SE0000000000".$digit : null;
                $num_row < 99 ? $documentNo = "SE00000000000".$digit : null;
                $num_row < 9 ? $documentNo = "SE000000000000".$digit : null;
                $num_row == 0 ? $documentNo = "SE000000000000".$digit : null;

                $booking_branch_id = $request->branch_id;
                $jobs_status = "new";
                $create_by = $request->customer_id;
    
                $booking = Booking::create([
                    'booking_no' => $documentNo,
                    'booking_branch_id' => $booking_branch_id,
                    'booking_sender_id' => $request->customer_id,
                    'booking_type' => $request->booking_type,
                    'booking_status' => $jobs_status,  
                    'create_by' => $user->employee_id
                ]);
                $booking_id = $booking->id;
                $bookingData = Booking::where('id',$booking_id)->first();
                $trackingList = Tracking::where('tracking_no', '!=', '')->where('tracking_no', 'NOT LIKE', '%Destroy')->where('tracking_booking_id',$booking_id)->get();
                $customer = Customer::where('id',$request->customer_id)->first();
                $currierList = Employee::where('emp_position', "พนักงานจัดส่งพัสดุ(Courier)")->where('emp_branch_id',$user->emp_branch_id)->where('emp_status',"1")->get();
                $employee = Employee::where('id',$user->employee_id)->first();
                return view('/input',compact(['bookingData','customer','trackingList','currierList','employee']));
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required'  
        ]);
        if ($validator->fails()) {
            alert()->error('ขออภัย', 'กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->to('/input/1');
        }
        $user = Auth::user();
        $booking = Booking::find($id);
        if($booking){
            $booking->update([
                'booking_sender_id' => $request->customer_id
            ]);
            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->to('/input/'.$user->emp_branch_id);
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function updateSenderBooking(Request $request, $id)
    {
        return "updateSenderBooking";
    }

    public function bookingList($id = null, $date = null) {
        $today = date('Y-m-d');
        // dd($date);
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();

        if($date !== null){
            $bookings = Booking::where('booking_branch_id', $id)->where('created_at', 'like', $date.'%')->get();
        }else{
            $date = date('Y-m-d');
            $bookings = Booking::where('booking_branch_id', $id)->where('created_at', 'like', $today.'%')->get();
        }
        return view('Receives/receive',compact(['bookings','employee','date']));
    }

    public function saveAndCloseBookingJobs(Request $request) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($request->id){
            //   ต้องเช็คก่อนว่ารายการด้านในสำเร็จหมดทุกรายการแล้วหรือยัง 
            $checkRow = Tracking::where('tracking_booking_id',$request->id)->get();
            if(count($checkRow) > 0){
                $checkRowSuccess = Tracking::where('tracking_booking_id',$request->id)
                                            ->where('tracking_amount', NULL)
                                            ->where('tracking_no', 'not like', "%Destroy%")
                                            ->orwhere('tracking_booking_id',$request->id)
                                            ->where('tracking_amount', '<=', '0')
                                            ->where('tracking_no', 'not like', "%Destroy%")
                                            ->get();
                if(count($checkRowSuccess)>0){
                    alert()->error('ขออภัย', 'ยังมีรายการย่อยที่ทำรายการไม่เสร็จ กรุณาทำให้เรียบร้อยก่อน')->showConfirmButton('ตกลง', '#3085d6')->showConfirmButton('ตกลง', '#3085d6');
                    return redirect()->back();
                }else{

                    $bookingData = Booking::find($request->id);
                    $booking_id = $bookingData->id;
                    $bookingData->update([
                        'booking_status' => "done",
                        'receive_money' => $request->receive_money
                    ]);

                    $trackings = Tracking::where('tracking_booking_id',$bookingData->id)->where('tracking_no', 'not like', "%Destroy%")->get();
                    $date = date('Y-m-d H:i:s');
                    foreach ($trackings as $tracking) {
                        $PostCode = PostCode::where('postcode',$tracking->receiver->cust_postcode)->first();
                        if($PostCode->drop_center_id == $employee->emp_branch_id){
                            $tracking->update([
                                'tracking_status' => "done",
                                'orther_dc_revice_time' => $date
                            ]);
                        }else{
                            $tracking->update([
                                'tracking_status' => "done"
                            ]);
                        }
                        $subTrackings = SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                        $i = 0;
                        foreach ($subTrackings as $subTracking) {
                            $i++;
                            $subTracking->update([
                                'subtracking_under_tracking_id' => $i
                            ]);
                        }
                        $TrackingsLogs = TrackingsLog::create([
                            'tracking_no' => $tracking->tracking_no, 
                            'tracking_receiver_id' => $tracking->tracking_receiver_id,
                            'tracking_status_id' => 1, 
                            'tracking_branch_id_dc' => $bookingData->booking_branch_id, 
                            'tracking_branch_id_sub_dc' => 0,
                            'tracking_date' => $date
                        ]);
                        
                        $PacelCare = PacelCare::create([
                            'tracking_id' => $tracking->id, 
                            'doing_by' => $employee->id,
                            'branch_id' => $bookingData->booking_branch_id, 
                            'status' => 1, 
                            'ref_no' => null
                        ]);
                    }

                    $subTrackings = SubTracking::where('subtracking_booking_id',$bookingData->id)->get();
                    foreach ($subTrackings as $subTracking) {
                        $subTracking->update([
                            'subtracking_status' => "done"
                        ]);
                    }
                    // /previewTrackingBarcode_all_booking/'.$booking_id.'
                    alert()->success('สำเร็จ', 'บันทึกข้อมูลสำเร็จ')->showConfirmButton('<a href="#" Onclick="open_btn_slip_barcode(\''.$booking_id.'\')" style="color:#fff; padding:30px 40px; margin:-30px -40px;">ตกลง</a>', '#3085d6');
                    return redirect()->to('/bookingList/'.$user->emp_branch_id);
                }
            }else{
                alert()->error('ขออภัย', 'ไม่พบรายการพัสดุในบิลนี้ กรุณาเพิ่มรายการก่อน')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->back();
            }
        }else{
            alert()->error('ขออภัย', 'เกิดข้อผิดพลาย กรุณาลองใหม่')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function connectBooking($id = null) {
        if($id) {
            $bookingData = Booking::where('id',$id)->first();
            if($bookingData->booking_type=='1'){
                $trackingLists = Tracking::where('tracking_booking_id',$id)->where('tracking_no', '')->where('tracking_amount', '0')->get();
                foreach ($trackingLists as $trackingList) {
                    $trackingList->delete();
                }
            }
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
           
            $bookingData = Booking::where('id',$id)->first();
            // dd($id);
            // $trackingList = Tracking::where('tracking_no', '!=', '')->where('tracking_no', 'NOT LIKE', '%Destroy')->where('tracking_booking_id',$id)->orwhere('tracking_amount', '>', '0')->where('tracking_no', 'NOT LIKE', '%Destroy')->where('tracking_booking_id',$id)->get();
            $trackingList = Tracking::where('tracking_no', '!=', '')->where('tracking_no', 'NOT LIKE', '%Destroy')->where('tracking_booking_id',$id)->orwhere('tracking_amount', '>', '0')->where('tracking_no', 'NOT LIKE', '%Destroy')->where('tracking_booking_id',$id)->get();
            $customer = Customer::where('id',$bookingData->booking_sender_id)->first();
            $currierList = Employee::where('emp_position', "พนักงานจัดส่งพัสดุ(Courier)")->where('emp_branch_id',$user->emp_branch_id)->where('emp_status',"1")->get();
            
            return view('/input',compact(['bookingData','customer','trackingList','currierList','employee']));
            
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
    
    public function find_mountgroup(){
        $sql = "SELECT SUBSTRING(a.created_at, 1, 7) AS mounth FROM bookings a GROUP BY mounth ORDER BY mounth DESC Limit 12";
        $bookmounts = DB::select($sql);
        // dd($bookmounts);
        return $bookmounts;
    }
    
    public function Income_summarymount($branch_id = null, $datefrom = null, $dateto = null){
        $user = Auth::user();
        $employee = Employee::find($user->employee_id);
        $dropCenter = DropCenter::find($branch_id);
        if($dateto == null){
            $bookings = Booking::where('booking_branch_id',$branch_id)->where('created_at','like', $datefrom.'%')->get();
        }else{
            $bookings = Booking::where('booking_branch_id',$branch_id)->whereBetween('created_at', [$datefrom." 00:00:00",$dateto." 23:59:59"])->get();
        }
        $pdf = PDF::loadView('/Receives.RequestReport',compact(['employee','dropCenter','bookings','datefrom','dateto']))->setPaper('A4', 'portrait');
        return $pdf->stream();
    }
}
