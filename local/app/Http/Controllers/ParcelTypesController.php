<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\ParcelType;
use Validator;
use Auth;

class ParcelTypesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'parcel_type_name' => 'required',
            'parcel_type_description' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('ขออภัย','กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }

        ParcelType::create([
            'parcel_type_name' => $request->parcel_type_name,
            'parcel_type_description' => $request->parcel_type_description,
            'parcel_type_status' => true
        ]);
        alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง','#3085d6');
        return redirect()->to('/parceltype_get_list/1');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id  
     * @return \Illuminate\Http\Response
     */

     public function parcelTypeList($id = null) {
        $parcelTypes = ParcelType::get();
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();

        return view('ManagementMenu.parcel_type_management',compact(['parcelTypes','employee']));
     }

    //  แสดงรายการข้อมูลเดิม เพื่อpreviewก่อนแก้ไข
    public function show($id)
    {
        $parcelType = ParcelType::where('id', $id)->first();
        if($parcelType){
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();

            return view('ManagementMenu.parcel_type_create', compact(['parcelType','employee']));
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'parcel_type_name' => 'required',
            'parcel_type_description' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('ขออภัย','กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }

        $parcelType = ParcelType::find($id);
        if($parcelType){
            $parcelType->update([
                'parcel_type_name' => $request->parcel_type_name,
                'parcel_type_description' => $request->parcel_type_description,
                'parcel_type_status' => true

            ]);
            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->to('/parceltype_get_list/1');
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    }  

    public function parcel_type_create() {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
       
        return view('ManagementMenu.parcel_type_create',compact('employee'));
    }
}
