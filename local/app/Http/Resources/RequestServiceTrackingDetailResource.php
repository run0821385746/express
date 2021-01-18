<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\Booking;
use App\Model\Customer;
use App\Model\SubTracking;
use App\Http\Resources\DimensionHistoryResource;


class RequestServiceTrackingDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $booking = Booking::where('id',$this->tracking_booking_id)->first();
        $sender =  Customer::where('id',$booking->booking_sender_id)->first();
        $receiver = Customer::where('id',$this->tracking_receiver_id)->first();
        $subTrackingList = SubTracking::where('subtracking_tracking_id',$this->id)->get();
        // return $subTrackingList;
        $dimensionHistoryResource = DimensionHistoryResource::collection($subTrackingList);
        return [
            'tracking_id' => $this->id,
            'booking' => $booking,
            'receiver' => $receiver,
            'sender' => $sender,
            'trackings' => $dimensionHistoryResource
        ];
    }
}
