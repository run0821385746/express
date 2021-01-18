<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'emp_firstname' => $this->employee->emp_firstname,
            'emp_lastname' => $this->employee->emp_lastname,
            $this->mergeWhen($request->is('api/login'), [
                'token' => $this->createToken('kts_system')->accessToken,
            ]),
            $this->mergeWhen($request->is('api/employee-data'), [
                'idEmployee' => !empty($this->employee->id) ? $this->employee->id : null,
                'imageEmployee' => !empty($this->employee->emp_image) ? url($this->employee->emp_image) : null,
                'nameEmployee' => !empty($this->employee->emp_firstname) ? $this->employee->emp_firstname : null,
                'phoneEmployee' => !empty($this->employee->emp_phone)? $this->employee->emp_phone : null,
                'email' => $this->email,
            ]),
        ];
    }
}
