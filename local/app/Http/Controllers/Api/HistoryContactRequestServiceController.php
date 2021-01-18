<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\HistoryContactRequestContact;
use Validator;
use Auth;

class HistoryContactRequestServiceController extends Controller
{
    public function createHistoryContactRequestService(Request $request , $id) {
        // return $id;
        if($id){
            $user = Auth::user();
                $validator = Validator::make($request->all(),[
                    'history_booking_id' => 'required',
                    'history_reason_id' => 'required',
                    'history_timing_call' => 'required'
                ]);
        
                if($validator->fails()) {
                    return _res(false, null, 'ข้อมูลไม่ครบ', null);
                }
    
                HistoryContactRequestContact::create([
                    'history_booking_id' => $request->history_booking_id,
                    'history_currier_id' => $user->id,
                    'history_reason_id' => $request->history_reason_id,
                    'history_reason_detail' => $request->history_reason_detail,
                    'history_timing_call' => $request->history_timing_call,
                    'history_status' => 'active',
                    'history_branch_id' => $user->id,
                ]);
                return _res(true, null, 'บันทึกข้อมูลสำเร็จ', null);
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);

        }
    }

    public function createRequestServiceStatusWrong(Request $request) {
        $user = Auth::user();
        $validator = Validator::make($request->all(),[
            'history_booking_id' => 'required',
            'history_reason_detail' => 'required'
        ]);

        if($validator->fails()) {
            return _res(false, null, 'ข้อมูลไม่ครบ', null);
        }
        
        HistoryContactRequestContact::create([
            'history_booking_id' => $request->history_booking_id,
            'history_currier_id' => $user->id,
            'history_reason_id' => '-',
            'history_reason_detail' => $request->history_reason_detail,
            'history_timing_call' => '-',
            'history_status' => 'active',
            'history_branch_id' => $user->id
        ]);
        return _res(true, null, 'บันทึกข้อมูลสำเร็จ', null);
    }
}
