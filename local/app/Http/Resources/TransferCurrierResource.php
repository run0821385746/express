<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SubTrackingResource;
use App\Model\SubTracking;

class TransferCurrierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $subTrackings = SubTracking::where('subtracking_tracking_id',$this->id)->get();
        $tracking_cod = 0;
        foreach ($subTrackings as $subTracking) {
            $tracking_cod += $subTracking->subtracking_cod;
        }

        return [
            'id' => $this->id,
            'tracking_no' => $this->tracking->tracking_no,
            'tracking_cod_amount' => number_format($tracking_cod,2),
            // 'subTracking' => $subTrackings
        ];
    }
}
