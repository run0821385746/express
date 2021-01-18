<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\District;
use App\Http\Resources\DistrictResource;


class PostCodeResource extends JsonResource
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
        $districtResource =  DistrictResource::collection($district);
        return [
            'id' => $this->id,
            'postcode' => $this->postcode,
            'province' => $this->province,
            'district' => $districtResource,
        ];
    }
}
