<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\Booking;
use App\Model\RequestService;
use Validator;
use Auth;
use DataTables;
use DB;

class RequestServicesController extends Controller
{
      
    public function getRequestServiceList($id = null) {
       
        if($id){
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            return view('/RequestServices.request_service_list',compact(['employee','id']));

        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }  
    
    public function getRequestServiceListDatatable(Request $request) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $requestServiceList = RequestService::where('branch_id', $user->emp_branch_id)->whereDate('created_at', DB::raw('CURDATE()'))->get();

        return Datatables::of($requestServiceList)
        ->addIndexColumn()
        ->editColumn('request_sender_id', function($row){
            return $row->sender->cust_name;
        })
        ->editColumn('request_currier_id', function($row){
            return $row->courier->emp_firstname.' '.$row->courier->emp_lastname;
        })
        ->editColumn('request_parcel_qty', function($row){
            if($row->request_parcel_qty == '1'){
                return "1 ชิ้น";
            }else if($row->request_parcel_qty == '2'){
                return "2 ชิ้น";
            }else if($row->request_parcel_qty == '3'){
                return "3 ชิ้น";
            }else if($row->request_parcel_qty == '4'){
                return "4 ชิ้น";
            }else if($row->request_parcel_qty == '5'){
                return "5-10 ชิ้น";
            }else if($row->request_parcel_qty == '6'){
                return "มากกว่า 10 ชิ้น";
            }
        })
        ->editColumn('request_status', function($row){
            if($row->request_status == 'request'){
                return "จ่ายงานเข้ารับ";
            }else if($row->request_status == 'stuck'){
                return "ติดปัญหาการเข้ารับ";
            }else if($row->request_status == "request-done"){
                return "<p style='color:green;'>เข้ารับสำเร็จ</p>";
            }else if($row->request_status == "done"){
                return "<p style='color:green;'>ส่งเข้าสาขาสำเร็จ</p>";
            }else if($row->request_status == "cancel"){
                $employee = Employee::where('id',$row->action_status)->first();
                return "ยกเลิกโดย : ".$employee->emp_firstname." ".$employee->emp_lastname;
            }
        })
        ->addColumn('action', function($row){
            if($row->request_status == "cancel"){
                return "<button type='button' class='btn btn-danger btn-sm'>ยกเลิกจาก DC</button>";
            }else{
                if($row->request_status == "request"){
                    $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE request_service_id = '$row->id' AND courier_id = '$row->request_currier_id' order by created_at asc";
                    $courier_call_lists = DB::select($sql);
                    // $call_status = json_encode($courier_call_lists, JSON_UNESCAPED_UNICODE);

                    $call_zero = 0;
                    $call_one = 0;
                    $call_two = 0;
                    foreach ($courier_call_lists as $courier_call_list) {
                        if($courier_call_list->callstatus == 0){
                            $call_zero += 1;
                        }else if($courier_call_list->callstatus == 1){
                            $call_one += 1;
                        }else if($courier_call_list->callstatus == 2){
                            $call_two += 1;
                        }
                    }
                    if($call_zero >= 1){
                        $color = "primary";
                        $status = "พร้อมเข้ารับพัสดุ";
                    }else if($call_one >= 1){
                        $color = "danger";
                        $status = "ยกเลิการเข้ารับ/เบอร์ผิด";
                    }else if($call_two >= 3){
                        $color = "danger";
                        $status = "ติดปัญหา";
                    }else if($call_two >= 1){
                        $color = "primary";
                        $status = "รอเข้ารับ";
                    }else{
                        $color = "secondary";
                        $status = "รอเข้ารับ";
                    }
                    if($row->action_status !== null){
                        $color = "danger";
                        $status = "ติดปัญหา";
                    }
                    return "<button type='button' onclick=\"viewstuck('$row->id','$color','$status','$row->request_currier_id','$row->action_status')\" class='btn btn-$color btn-sm'>".$status."</button>";
                }else{
                    $sql = "SELECT callstatus, note, oncall, ontalk, callTime FROM courier_calls WHERE request_service_id = '$row->id' AND courier_id = '$row->request_currier_id' order by created_at asc";
                    $courier_call_lists = DB::select($sql);
                    // $call_status = json_encode($courier_call_lists, JSON_UNESCAPED_UNICODE);

                    $call_zero = 0;
                    $call_one = 0;
                    $call_two = 0;
                    foreach ($courier_call_lists as $courier_call_list) {
                        if($courier_call_list->callstatus == 0){
                            $call_zero += 1;
                        }else if($courier_call_list->callstatus == 1){
                            $call_one += 1;
                        }else if($courier_call_list->callstatus == 2){
                            $call_two += 1;
                        }
                    }
                    if($call_zero >= 1){
                        $color = "primary";
                        $status = "พร้อมเข้ารับพัสดุ";
                    }else if($call_one >= 1){
                        $color = "danger";
                        $status = "ยกเลิการเข้ารับ/เบอร์ผิด";
                    }else if($call_two >= 3){
                        $color = "danger";
                        $status = "ติดปัญหา";
                    }else if($call_two >= 1){
                        $color = "primary";
                        $status = "รอเข้ารับ";
                    }else{
                        $color = "secondary";
                        $status = "รอเข้ารับ";
                    }
                    if($row->action_status !== null){
                        $color = "danger";
                        $status = "ติดปัญหา";
                    }
                    if($row->request_status == "request-done" || $row->request_status == "done"){
                        $status = "ประวัติการติดต่อ";
                    }
                    $btn = "<button type='button' onclick=\"viewstuck('$row->id','$color','$status','$row->request_currier_id','$row->action_status')\" class='btn btn-$color btn-sm'>".$status."</button>";
                    if($row->request_status == "request-done" || $row->request_status == "done"){
                        $btn .= " <a href='/getReceive_bycourier_Detail/$row->request_booking_id'><button type='button' class='btn btn-success btn-sm'>รายละเอียด</button>";
                    }
                    return $btn;
                }
            }
        })
        ->rawColumns(['action' => 'action','request_status' => 'request_status'])
        ->make(true);
    }  

    public function saveRequestServiceBookingJobs(Request $request, $id) {
        $user = Auth::user();
        if($id){
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required',
                'request_currier_id' => 'required',
                'request_parcel_qty' => 'required',
            ]);
           
            if($validator->fails()) {
                alert()->error('ขออภัย', ' ขออภัย กรุณาลองใหม่')->showConfirmButton("ตกลง","#3085d6");
                return redirect()->back();
            } 
    
            $booking = Booking::find($id);
            $booking->update([
                'booking_status' => 'request'
            ]);
            $requestService = RequestService::where('request_booking_id', $booking->id)->where('request_booking_no', $booking->booking_no)->first();
            if(empty($requestService)){
                $requestService = RequestService::create([
                    'request_booking_id'  => $request->booking_id, 
                    'request_sender_id'  => $booking->booking_sender_id, 
                    'request_currier_id'  => $request->request_currier_id,  
                    'request_status'  => "request",
                    'branch_id'  => $user->emp_branch_id,
                    'request_parcel_qty'  => $request->request_parcel_qty,  
                    'request_booking_no'  => $booking->booking_no,  
                ]);
            }else{
                $requestService->update([
                    'request_currier_id'  => $request->request_currier_id,
                    'request_parcel_qty'  => $request->request_parcel_qty, 
                    'action_status'  => NULL, 
                    'request_status'  => "request"
                ]);
            }
    
            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->to('/getRequestServiceList/'.$user->emp_branch_id);
        
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
}
