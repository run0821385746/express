<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PostCodeResource;
use App\Model\PostCode;
use App\Model\District;
use App\Model\SubDistrict;
use Auth;

class PostCodeController extends Controller
{
    public function getPostCode() {
        $user = Auth::user();
        if($user){
            $postcode = PostCode::get();
            $postcodeResource = PostCodeResource::collection($postcode);
            return $postcodeResource;

        }else{
            return _res(false, null, 'ไม่พบข้อมูล login', null);
        }
    }

    public function searchPostCode($id = null) {
        if($id) {
            $postcode = PostCode::where('postcode',$id)->first();
            if($postcode){
                $district = District::where('postcode_id',$postcode->id)->get();
                $subDistrict = SubDistrict::where('postcode_id',$postcode->id)->get();
                return compact('postcode','district','subDistrict');
            }else{
                return _res(false, null, 'ไม่พบข้อมูล', null);
            }
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }

    public function searchDistrict($id = null) {  //district id
        if($id) {
            $subDistrict = SubDistrict::where('district_id',$id)->get();
            if($subDistrict){
                return $subDistrict;
            }else{
                return _res(false, null, 'ไม่พบข้อมูล', null);
            }
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }
}
