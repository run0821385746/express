<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\ProductPrice;
use Validator;
use Auth;

class ProductPricesController extends Controller
{
  
     

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'product_width' => 'required',
            'product_hight' => 'required',
            'product_length' => 'required',
            'product_price' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('ขออภัย','กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }

        $dimension = $request->product_width+$request->product_hight+$request->product_length;
        ProductPrice::create([
            'product_name' => $request->product_name, 
            'product_width' => $request->product_width, 
            'product_hight' => $request->product_hight, 
            'product_length' => $request->product_length, 
            'product_dimension' => $dimension,
            'product_price_status' => true,
            'product_price' => $request->product_price
        ]);

        alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง','#3085d6');
        return redirect()->to('product_price_get_list/1');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function productPriceList($id = null) {
        $productPrices = ProductPrice::get();
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
       
        return view('ManagementMenu.product_price_management',compact(['productPrices','employee']));
     }

    public function show($id)
    {
        $productPrice = ProductPrice::where('id', $id)->first();
        if($productPrice){
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();

            return view('ManagementMenu.product_price_create', compact(['productPrice','employee']));
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
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'product_width' => 'required',
            'product_hight' => 'required',
            'product_length' => 'required',
            'product_price' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('ขออภัย','กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }

        $productPrics = ProductPrice::find($id);
        $dimension = $request->product_width+$request->product_hight+$request->product_length;
        if($productPrics){
            $productPrics->update([
            'product_name' => $request->product_name, 
            'product_width' => $request->product_width, 
            'product_hight' => $request->product_hight, 
            'product_length' => $request->product_length, 
            'product_dimension' => $dimension, 
            'product_price_status' => true,
            'product_price' => $request->product_price 

            ]);
            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->to('/product_price_get_list/1');
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    }

    public function product_price_create() {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
    
        return view('ManagementMenu.product_price_create',compact('employee'));
    }
}
