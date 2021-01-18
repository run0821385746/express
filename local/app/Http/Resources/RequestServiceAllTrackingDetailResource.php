<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\Customer;
use App\Model\Booking;
use App\Model\Tracking;
use App\Model\SubTracking;

class RequestServiceAllTrackingDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
   
    public function toArray($request)
    {
        $sender =  Customer::where('id',$this->booking_sender_id)->first();
        $booking = Booking::find($this->id);
        $tracking = Tracking::where('tracking_booking_id',$booking->id)->get();
        $subTrackingList = SubTracking::where('subtracking_booking_id',$this->id)->get();
        $dimensionHistoryResource = DimensionHistoryResource::collection($subTrackingList);
        return [
            'booking' => $booking,
            'sender' => $sender,
            'tracking_id' => $tracking,
            'subtrackings' => $dimensionHistoryResource
        ];
    }
}
