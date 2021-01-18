<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\PostCode;
use Validator;

class PostCodesController extends Controller
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
    public function store(Request $request)
    {
        return $request->all();
        $validator = Validator::make($request->all(), [
            'postcode' => 'required',
            'province' => 'required'
        ]);
        if($validator->fails()) {
            alert()->error('ขออภัย', 'กรอกข้อมูลให้ครบ')->showConfirmButton("ตกลง","#3085d6");
            return redirect()->back();
        }

        PostCode::create([
            'postcode' => $request->postcode,
            'province' => $request->province
        ]);
        alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
        return redirect()->to('/postcode_get_list/1');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postCodeGetList($id = null) {
        $postcode = PostCode::where('id', $id)->get();
        if($postcode){
            return view('ManagementMenu.postcode_search', compact('postcode'));
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        $postcode = PostCode::get();
        if($postcode){
            return view('Employee.emp_create', compact('postcode'));
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
        $postcode = PostCode::find($id);
        if($postcode){
            $postcode->update([
                'postcode' => $request->postcode,
                'province' => $request->province
            ]);
            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->to('/postcode_get_list/1');
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
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
