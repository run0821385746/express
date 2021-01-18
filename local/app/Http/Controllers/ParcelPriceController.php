<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\ParcelPrice;
use Validator;
use Auth;


class ParcelPriceController extends Controller
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
            'parcel_total_dimension' => 'required',
            'parcel_price' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('ขออภัย','กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }

        ParcelPrice::create([
            'parcel_total_dimension' => $request->parcel_total_dimension,
            'parcel_total_weight' => $request->parcel_total_weight * 1000,
            'parcel_price' => $request->parcel_price,
            'parcel_price_status' => true
        ]);
        alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง','#3085d6');
        return redirect()->to('/parcel_price_get_list/1');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function parcelPriceList($id = null) {
        $parcelPrices = ParcelPrice::where('parcel_total_dimension','!=','COD')->get();
        $CODPrices = ParcelPrice::where('parcel_total_dimension','=','COD')->first();
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
       
        return view('ManagementMenu.parcel_price_management',compact(['parcelPrices','employee','CODPrices']));
     }


     public function show($id)
     {
         $parcelPrice = ParcelPrice::where('id', $id)->first();
         if($parcelPrice){
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
           
             return view('ManagementMenu.parcel_price_create', compact(['parcelPrice','employee']));
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
            'parcel_total_dimension' => 'required',
            'parcel_total_weight' => 'required',
            'parcel_price' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('ขออภัย','กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
        $parcel_total_weight = $request->parcel_total_weight*1000;
        $parcelPrices = ParcelPrice::find($id);
        if($parcelPrices){
            $parcelPrices->update([
                'parcel_total_dimension' => $request->parcel_total_dimension,
                'parcel_total_weight' => $parcel_total_weight,
                'parcel_price' => $request->parcel_price,
                'parcel_price_status' => $request->parcel_price_status
            ]);
            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->to('/parcel_price_get_list/1');
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    }

    public function parcel_price_create(){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
       
        return view('ManagementMenu.parcel_price_create',compact('employee'));
    }
    
    public function priceCOD(Request $request){
        $CODPrices = ParcelPrice::where('parcel_total_dimension','=','COD')->first();
        $CODPrices->update([
            'parcel_price' => $request->CODPrice
        ]);
       
        return '{"status":"1","msg":"แก้ไขสำเร็จ"}';
    }
}
