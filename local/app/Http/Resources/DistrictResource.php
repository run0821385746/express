<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SubDistrictResource;
use App\Model\District;

class DistrictResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $district = District::where('postcode_id',$this->id)->get();
        $subDistrictResource = SubDistrictResource::collection($district);
       
        // return $subDistrictResource;
        return [
            'id' => $this->id,
            'district_name' => $district,
            // 'subDistrictResource' => $subDistrictResource,
        ];
    }
}
