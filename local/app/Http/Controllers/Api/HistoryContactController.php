<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\HistoryContact;
use App\Model\Employee;
use App\Model\Transfer;
use Validator;
use Auth;

class HistoryContactController extends Controller {

    public function getHistoryContactList($id = null) {
        if($id) {
            $historyContactList = HistoryContact::where('history_tracking_id',$id)->get();
            if($historyContactList){
                return $historyContactList;
            }else{
                return _res(false, null, 'ไม่พบข้อมูล', null);
            }
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }

    public function createHistoryContact(Request $request) {

        $validator = Validator::make($request->all(),[
            'history_tracking_id' => 'required',
            'history_reason_id' => 'required',
            'history_timing_call' => 'required'
        ]);

        if($validator->fails()) {
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
        
        $user = Auth::user(); 
        if($user){
            $employeeData = Employee::where('id', $user->employee_id)->first();
            HistoryContact::create([
                'history_tracking_id' => $request->history_tracking_id,
                'history_currier_id' => $user->id,
                'history_reason_id' => $request->history_reason_id,
                'history_timing_call' => $request->history_timing_call,
                'history_status' => 'active',
                'history_branch_id' => $employeeData->dropCenter->id
            ]);


            $historyCount = HistoryContact::where('history_tracking_id',$request->history_tracking_id)->get();
            if($historyCount){
                if(count($historyCount)>2){
                    $transfer = Transfer::where('transfer_tracking_id',$request->history_tracking_id)->first();
                    $transfer->update([
                    'action_status' => 'wrong'
                    ]);
                    return _res(true, null, 'บันทึกสำเร็จ', null);
                }else{
                    $transfer = Transfer::where('transfer_tracking_id',$request->history_tracking_id)->first();
                    $transfer->update([
                        'action_status' => 'history'
                    ]);
                    return _res(true, null, 'บันทึกสำเร็จ', null);
                }
            }else{
                $transfer = Transfer::where('transfer_tracking_id',$request->history_tracking_id)->first();
                $transfer->update([
                    'action_status' => 'history'
                ]);
                return _res(true, null, 'บันทึกสำเร็จ', null);
            }
           
        }else{
            return _res(false, null, 'ขออภัย', null);
        }
    }
}
