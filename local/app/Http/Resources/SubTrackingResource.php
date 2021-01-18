<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\DimensionHistory;

class SubTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $dimensionHistory = DimensionHistory::where('dimension_history_subtracking_id',$this->id)->first();
        return [
            'id' => $this->id,
            'subtracking_no' => $this->subtracking_no,
            'dimension_history_width' => $dimensionHistory->dimension_history_width,
            'dimension_history_hight' => $dimensionHistory->dimension_history_hight,
            'dimension_history_length' => $dimensionHistory->dimension_history_length,
        ];
    }
}
