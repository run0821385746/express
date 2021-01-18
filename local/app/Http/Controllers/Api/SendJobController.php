<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\Transfer;
use App\Model\Tracking;
use App\Model\ParcelReceive;
use App\Http\Resources\TransferCurrierResource;
use App\Http\Resources\ParcelSendingDoneResource;
use Validator;
use Image;
use Auth;
use Storage;

class SendJobController extends Controller
{
    public function createReceiveParcelDetail(Request $request) {
        $validator = Validator::make($request->all(), [
            'receiver_tracking_id' => 'required',
            'receiver_signatur' => 'required',
            'receiver_name' => 'required',
            'receiver_type_id' => 'required'
        ]);

        if ($validator->fails()) { 
            $errors = $validator->errors();
            return _res(false, null, ' ข้อมูลไม่ครบ', null);
        }

        $user = Auth::user();
        if($user){
            $employeeData = Employee::where('id', $user->employee_id)->first();

            if($request->receiver_image){
                $file = $request->file('receiver_image');
                $path = $file->hashName('public/image_receiver'); 
                $receiver_image = Image::make($file);
                Storage::put($path, (string) $receiver_image->encode('jpg', 75));
                $receiver_image_url = Storage::url($path);
            
            }else{
                $receiver_image_url = null;
            }

            if($request->receiver_signatur){
                $image_signatur_path_file = $request->file('receiver_signatur');
                $image_signatur_path = $image_signatur_path_file->hashName('public/image_signatur'); 
                $receiver_signatur = Image::make($image_signatur_path_file);
                Storage::put($image_signatur_path, (string) $receiver_signatur->encode('jpg', 75));
                $receiver_signatur_url = Storage::url($image_signatur_path);
            
            }else{
                $receiver_image_url = null;
            }

            ParcelReceive::create([
                'receiver_tracking_id' => $request->receiver_tracking_id,
                'receiver_image' => $receiver_image_url,
                'receiver_signatur' => $receiver_signatur_url,
                'receiver_name' => $request->receiver_name,
                'receiver_type_id' => $request->receiver_type_id,
                'receiver_other_type_name' => $request->receiver_other_type_name,
                'receiver_branch_id' => $employeeData->dropCenter->id,
                'receiver_currier_id' => $user->id,
                'receiver_status' => 'active'
            ]);

            $transfer = Transfer::where('transfer_tracking_id',$request->receiver_tracking_id)->first();  
            // return $transfer;
            // return $request->receiver_tracking_id;

            if($transfer){
                $transfer->update([
                    'action_status' => 'done'
                ]);
                return _res(true, null, 'บันทึกสำเร็จ', null);
            }else{
                return _res(false, null, 'ไม่เจอ tracking id', null);
            }
        }else{
            return _res(false, null, 'ขออภัย ไม่พบข้อมูล', null);
        }
    }

    public function getCodList() {
        $user = Auth::user();
        if($user){
            $currierTransferList = Transfer::where('transfer_courier_id',$user->id)
            ->where('action_status','done')
            ->get();

            $transferResource = TransferCurrierResource::collection($currierTransferList);
            return $transferResource;

        }else{
            return _res(false, null, 'ขออภัย ไม่พบข้อมูล', null);
        }
    }

    public function getSendTrackingDetail($id = null) {
        if($id){
            $user = Auth::user();
                $tracking = Tracking::where('id',$id)->first();
                $parcelSendingDoneResource = new ParcelSendingDoneResource($tracking);
                
                if($parcelSendingDoneResource){
                    return $parcelSendingDoneResource;
                }else{
                    return _res(false, null, 'ขออภัย ไม่พบข้อมูล', null);
                }
           
        }else{
            return _res(false, null, 'ขออภัย ไม่พบข้อมูล', null);
        }
    }
}
   