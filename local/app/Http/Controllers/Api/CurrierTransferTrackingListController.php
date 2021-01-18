<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Transfer;
use App\Http\Resources\CurrierTrackingResource;
use Auth;

class CurrierTransferTrackingListController extends Controller
{
    public function getCurrierTrackingStatusDoingList() {
        $user = Auth::user();
        $currierTrackingList = Transfer::where('transfer_courier_id',$user->id)
        ->where('transfer_status','TransferToCourier')
        ->where('action_status','!=','done')
        ->get();
        $currierTrackingResource = CurrierTrackingResource::collection($currierTrackingList);
        return $currierTrackingResource;
   }  

    public function getCurrierTrackingStatusDoneList() {
        $user = Auth::user();
        $currierTrackingList = Transfer::where('transfer_courier_id',$user->id)
        ->where('transfer_status','TransferToCourier')
        ->where('action_status', 'done')
        ->get();
        $currierTrackingResource = CurrierTrackingResource::collection($currierTrackingList);
        return $currierTrackingResource;
    }

    public function changeStatusForCloseJobs(){
        $user = Auth::user();
        $transfers = Transfer::where('transfer_courier_id',$user->id)
        ->where('transfer_status','TransferToCourier')
        ->where('action_status', 'done')
        ->get();
        if($transfers){
            foreach ($transfers as $transfer) {
                $transfer->update([
                    'transfer_status' => 'Send-Done'
                ]);
            }
            return _res(true, null, 'บันทึกสำเร็จ', null);
        }else{
            return _res(false, null, 'เกิดข้อผิดพลาด', null);
        }
    }
}
