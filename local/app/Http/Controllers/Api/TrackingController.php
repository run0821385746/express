<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Tracking;
use App\Http\Resources\TrackingResource;
use App\Http\Resources\TrackingDetailResource;
use Auth;

class TrackingController extends Controller
{
   public function getTrackingList() {
        $user = Auth::user();
        $trackingList = Tracking::where('tracking_status','done')->get();
        $trackingResource = TrackingResource::collection($trackingList);
        return $trackingResource;
   }

   public function getTrackingDetail($id = null) {
      // return $id;
      if($id) {
         $tracking = Tracking::where('id',$id)->first();
         $trackingResource = new TrackingDetailResource($tracking);
         if($trackingResource){
            return $trackingResource;
         }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
         }
      }else{
         return _res(false, null, 'ไม่สามารถระบุ tracking no ได้ กรุณาลองใหม่', null);
      }
   }

   public function getTrackingDetailWithTrackingNo($id = null) {
      if($id) {
         $tracking = Tracking::where('tracking_no',$id)->get();
         if(count($tracking) > 0){
            if(count($tracking) > 1){
               return _res(false, null, 'ขออภัย', null);
            }else{
               return $tracking;
            }
         }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
         }
      }else{
         return _res(false, null, 'ไม่พบข้อมูล', null);
      }
   }
}
