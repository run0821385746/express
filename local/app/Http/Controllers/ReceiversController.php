<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Customer;
use App\Model\Employee;
use App\Model\province;
use Validator;
use Auth;

class ReceiversController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //  สร้างข้อมูลผู้รับพัสดุ 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cust_name' => 'required',
            'cust_address' => 'required',
            'cust_sub_district' => 'required',
            'cust_district' => 'required',
            'cust_province' => 'required',
            'cust_postcode' => 'required',
            'cust_phone' => 'required'    
        ]);
        if ($validator->fails()) {
            alert()->error('ขออภัย', 'กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }

        Customer::create([ 
            'cust_name' => $request->cust_name,
            'cust_address' => $request->cust_address,
            'cust_sub_district' => $request->sub_district,
            'cust_district' => $request->district,
            'cust_province' => $request->province,
            'cust_postcode' => $request->postcode,
            'cust_phone' => $request->cust_phone,
            'cust_status' => true
        ]);
        alert()->success('สำเร็จ', 'บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
        return redirect()->to('/receive_add_parcel');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function receiver_search(Request $request)
    {
        // return $request->search_phone;
        $validator = Validator::make($request->all(), [
            'search_phone' => 'required'
        ]);
        if ($validator->fails()) {
            alert()->error('ขออภัย', 'กรุณากรอกเบอร์โทร')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
        
        $customers = Customer::where('cust_phone', $request->search_phone)->get();
        return view('Customers.receiver_search',compact('customers'));
    }

    public function receiver_search_receive(Request $request)
    {
        $tracking_id = $request->tracking_id;
        $validator = Validator::make($request->all(), [
            'search_phone' => 'required'
        ]);
        if ($validator->fails()) {
            alert()->error('ขออภัย', 'กรุณากรอกเบอร์โทร')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
        $search_phone = $request->search_phone;
        $provinces = province::get();
        $customers = Customer::where('cust_phone','like', '%'. $request->search_phone.'%')->get();
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
       
        return view('Customers.customer_search_receive',compact(['customers','tracking_id','search_phone','employee','provinces']));
    }

    // เลือกข้อมูลผู้รับพัสดุ  
    public function show($id)
    {
        //  return $id;
         $customer = Customer::find($id);  
      
         if($customer){
            return view('Receives.receive_add_parcel',compact('customer'));
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}