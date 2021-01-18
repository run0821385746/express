<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\DimensionHistory;
use App\Model\ParcelType;

class DimensionHistoryResource extends JsonResource
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
        $parcelType = ParcelType::where('id',$this->subtracking_parcel_type)->first();
        
        return [
            'subtracking_id' => $this->id,
            'subtracking_price' => $this->subtracking_price,
            'dimensionHitory' => $dimensionHistory,
            'parcelType' => $parcelType
        ];
    }
}
