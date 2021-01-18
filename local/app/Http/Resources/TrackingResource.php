<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SubTrackingResource;
use App\Model\SubTracking;
use App\Model\HistoryContact;

class TrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $subTracking = SubTracking::where('subtracking_tracking_id',$this->id)->get();
        $subTrackingResource = SubTrackingResource::collection($subTracking);

         return [
            'id' => $this->id,
            'tracking_no' => $this->tracking_no,
            'receive_phone' => $this->receiver->cust_phone,            
            'subTracking' => $subTrackingResource
        ];
    }
}
