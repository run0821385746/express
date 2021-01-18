<?php

namespace App\Http\Resources;
use App\Model\HistoryContact;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrierTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $historyContact = HistoryContact::where('history_tracking_id',$this->id)->get();
        if($historyContact){
            $countRow = count($historyContact);
        }else{
            $countRow = 0;
        }
        
        return [
            'id' => $this->id,
            'transfer_tracking_id' => $this->transfer_tracking_id,
            'transfer_tracking_no' => $this->tracking->tracking_no,
            'transfer_cust_name' => $this->tracking->receiver->cust_name,
            'transfer_cust_phone' => $this->tracking->receiver->cust_phone,
            'transfer_status' => $this->transfer_status,
            'action_status' => $this->action_status,
            'history_contact_amount' => $countRow
        ];
    }
}
