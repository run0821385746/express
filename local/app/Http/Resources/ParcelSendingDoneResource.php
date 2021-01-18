<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\ParcelReceive;
use App\Model\Customer;
use App\Model\Tracking;
use App\Model\SubTracking;

class ParcelSendingDoneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tracking = Tracking::where('id',$this->id)->first();
        $subTrackings = SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
        $cod_amount = 0 ;
        foreach($subTrackings as $subTracking) {
            $cod_amount += $subTracking->subtracking_cod;
        }

        $customer = Customer::where('id',$tracking->tracking_receiver_id)->first();
        $parcelReceive = ParcelReceive::where('receiver_tracking_id',$this->id)->first();
       
        // return $parcelReceive->receiver_type_id;
       
        $parcelReceive->receiver_type_id = "1" ? $receiver_type_name = "ผู้รับรับเอง" : $receiver_type_name = "";
        $parcelReceive->receiver_type_id = "2" ? $receiver_type_name = "ญาติ" : $receiver_type_name = "";
        $parcelReceive->receiver_type_id = "3" ? $receiver_type_name = "นิติ" : $receiver_type_name = "";
        $parcelReceive->receiver_type_id = "4" ? $receiver_type_name = "รปภ" : $receiver_type_name = "";
        $parcelReceive->receiver_type_id = "5" ? $receiver_type_name = $parcelReceive->receiver_other_type_name : $receiver_type_name = "";

      
        return [
            'id' => $this->id,
            'tracking_no' => $parcelReceive->tracking->tracking_no,
            'receiver_image' => url($parcelReceive->receiver_image),
            'receiver_signatur' => url($parcelReceive->receiver_signatur),
            'receiver_name' => $parcelReceive->receiver_name,
            'receiver_type_id' => $parcelReceive->receiver_type_id,
            'receiver_type_name' => $parcelReceive->receiver_type_id,
            // 'receiver_other_type_name' => $parcelReceive->receiver_other_type_name,
            'receiver_currier_id' => $parcelReceive->receiver_currier_id,
            'receiver_status' => $parcelReceive->receiver_status,
            'created_at' => $parcelReceive->created_at,
            'updated_at' => $parcelReceive->updated_at,
            'receiver_id' => $customer,
            'cod_amount' => $cod_amount
        ];
    }
}
  