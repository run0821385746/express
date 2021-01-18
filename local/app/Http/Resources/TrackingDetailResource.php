<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\HistoryContactResource;
use App\Http\Resources\SubTrackingResource;

use App\Model\Booking;
use App\Model\HistoryContact;
use App\Model\Tracking;
use App\Model\SubTracking;
use App\Model\Customer;

// use App\Model\SubTracking;


class TrackingDetailResource extends JsonResource
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
        $subTrackings = SubTracking::where('subtracking_tracking_id',$this->id)->get();
        $cod_amount = 0;
        if($subTrackings){
            foreach($subTrackings as $subTracking){
                $cod_amount += $subTracking->subtracking_cod;
            }
        }
        
        $receiver_data = Customer::where('id',$this->tracking_receiver_id)->first();
        $historyContact = HistoryContact::where('history_tracking_id',$this->id)->get();
        return [
            'id' => $this->id,
            'booking_sender_id' => $booking->booking_sender_id,
            'tracking_no' => $this->tracking_no,
            'booking_sender_name' => $booking->customer->cust_name,
            'sender_data' => $booking,
            'cod_amount' => $cod_amount,
            'receiver_data' => $receiver_data,
            'history_call' => $historyContact
        ];
    }
}  
