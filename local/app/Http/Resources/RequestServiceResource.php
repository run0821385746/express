<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\Booking;
use App\Model\Customer;
use App\Model\HistoryContact;
use App\Model\HistoryContactRequestContact;

class RequestServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $customer = Customer::where('id',$this->request_sender_id)->first();
        $bookingJobs = Booking::where('booking_no',$this->request_booking_no)->first();
        $historyContact = HistoryContactRequestContact::where('history_booking_id',$bookingJobs->id)->get();
       
        return [
            'id' => $this->id,
            'request_status' => $this->request_status,
            'action_status' => $this->action_status,
            'booking_no' => $this->request_booking_no,
            'request_sender' => $customer,
            'booking_id' => $bookingJobs,
            'historyContact' => $historyContact
        ];
    }
}
