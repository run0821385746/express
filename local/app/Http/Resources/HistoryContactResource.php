<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\HistoryContact;

class HistoryContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $history = HistoryContact::where('history_tracking_id',$this->id)->get();
        $history_count = count($history);
        return [
            'id' => $this->id,
            'history_tracking_id' => $this->history_tracking_id,
            'history_reason_id' => $this->history_reason_id,
            'history_timing_call' => $this->history_timing_call
        ];
    }
}
