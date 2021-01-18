<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\ParcelWrongs;
use App\Model\Tracking;
use App\Model\PacelCare;
use App\Model\Employee;
use App\Model\TrackingsLog;
use App\Model\TransferDropCenter;
use App\Model\Customer;
use App\Model\PostCode;
use App\Model\Booking;
use Auth;

class ParcelWrongController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    public function getParcelWrongList() {
        $parcelWrongList = ParcelWrongs::get();
        if($parcelWrongList){
            return view('/ParcelStatusWrong.parcel_status_wrong',compact("parcelWrongList"));
        }
    }

    public function create_parcel_wrong(Request $request) {
        // return $request->all();
        if($request->tracking_id) {
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();

            $tracking = Tracking::find($request->tracking_id);
            $Booking = Booking::find($tracking->tracking_booking_id);
            $customerresive = Customer::find($tracking->tracking_receiver_id);
            $PostCode = PostCode::where('postcode', $customerresive->cust_postcode)->first();
            $parcelWrong = ParcelWrongs::create([
                'wrong_booking_id' => $tracking->tracking_booking_id,
                'wrong_tracking_id' => $request->tracking_id,
                'wrong_subtracking_id' => null,
                'wrong_problem_detail' => $request->wrong_problem_detail,
                'wrong_description_solve' => null,
                'wrong_status' => 'true'
            ]);

            $PacelCare = PacelCare::create([
                'tracking_id' => $tracking->id, 
                'doing_by' => $employee->id,
                'branch_id' => $employee->emp_branch_id, 
                'status' => 12, 
                'ref_no' => $parcelWrong->id
            ]);

            $date = date('Y-m-d H:i:s');
            $TrackingsLogs = TrackingsLog::create([
                'tracking_no' => $tracking->tracking_no, 
                'tracking_receiver_id' => $tracking->tracking_receiver_id,
                'tracking_status_id' => 14, 
                'tracking_branch_id_dc' => $Booking->booking_branch_id, 
                'tracking_branch_id_sub_dc' => $PostCode->drop_center_id,
                'tracking_date' => $date,
                'tracking_cause' => $request->wrong_problem_detail
            ]);

            if($Booking->booking_branch_id == $employee->emp_branch_id){
                $tracking->update([
                    'tracking_status' => 'ReceiveDoneReturn'
                ]);
            }else{
                $tracking->update([
                    'tracking_status' => 'ReturnBack'
                ]);
            }

            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();

        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล กรุณาทำรายการใหม่อีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function Cancel_StatusWrong($id = null){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($id != NULL){
            $tracking = Tracking::find($id);
            $parcelWrong = ParcelWrongs::where('wrong_tracking_id', $id)->where('wrong_status', 'true')->first();
            $parcelWrong->update([
                'wrong_status' => $employee->id
            ]);

            $TrackingsLog = TrackingsLog::where('tracking_no', $tracking->tracking_no)->where('tracking_status_id', '14')->orderby('tracking_date', 'Desc')->first();
            $TrackingsLog->delete();
            
            $PacelCare = PacelCare::where('tracking_id', $tracking->id)->where('status', '12')->orderby('created_at', 'Desc')->first();
            $PacelCare->delete();

            if($tracking->tracking_status == 'ReturnBack'){
                $tracking->update([
                    'tracking_status' => 'ReceiveDone'
                ]);
            }else{
                $tracking->update([
                    'tracking_status' => 'done'
                ]);
            }
            alert()->success('สำเร็จ','ยกเลิกรายการส่งกลับแล้ว')->showConfirmButton('ตกลง', '#3085d6');
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล กรุณาทำรายการใหม่อีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
        }
        return redirect()->back();
    }
}
