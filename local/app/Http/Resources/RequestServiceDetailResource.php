<?php

namespace App\Http\Resources;

use App\Model\Customer;
use App\Model\Booking;
use App\Model\HistoryContactRequestContact;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestServiceDetailResource extends JsonResource
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
        $customer = Customer::where('id',$booking->booking_sender_id)->first();
        $receiver = Customer::where('id',$this->tracking_receiver_id)->first();

        return [
            'id' => $this->id,
            'request_status' => $this->request_status,
            'action_status' => $this->action_status,
            'booking_no' => $this->booking_no,
            'sender' => $customer,
            'receiver' => $receiver
           
        ];
    }
}
