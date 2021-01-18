<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\DropCenter;
use App\Model\User;
use App\Model\PostCode;
use App\Model\province;
use App\Model\amphure;
use App\Model\District;
use App\Model\CourierArea;
use App\Model\AreaManagerBranch;
use App\Model\TransferDropCenterBill;
use DataTables;
use Validator;
use Auth;
use DB;

class DropCentersController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'drop_center_name' => 'required',
            'drop_center_address' => 'required',
            'drop_center_sub_district' => 'required',
            'drop_center_district' => 'required',
            'drop_center_province' => 'required',
            'drop_center_postcode' => 'required',
            'drop_center_phone' => 'required',
            'drop_center_name_initial' => 'required'
        ]);

        if ($validator->fails()) {
            alert()->error('ขออภัย','กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }  

        DropCenter::create([
            'drop_center_name' => $request->drop_center_name,
            'drop_center_address' => $request->drop_center_address,
            'drop_center_sub_district' => $request->drop_center_sub_district,
            'drop_center_district' => $request->drop_center_district,
            'drop_center_province' => $request->drop_center_province,
            'drop_center_postcode' => $request->drop_center_postcode,
            'drop_center_phone' => $request->drop_center_phone,
            'drop_center_status' => true,
            'drop_center_name_initial' => $request->drop_center_name_initial
        ]); 

        alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง','#3085d6');
        return redirect()->to('/dropcenter_get_list/1');
    }

    /** 
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function dropCenterGetList($id = null) {
        // $dropcenters = DropCenter::get();  
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
       
        return view('ManagementMenu.drop_center_management',compact(['id','employee']));
    } 

    public function dropCenterGetListDataTable(Request $request) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == "เจ้าของกิจการ(Owner)"){

            $dropcenters = DropCenter::get();  

        }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            $dropcenters = DropCenter::get();  

        }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            $dropcenters = DropCenter::where('id', $employee->emp_branch_id)->get();  

        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }

        return Datatables::of($dropcenters)
        ->editColumn('drop_center_address', function($row){
            return $row->drop_center_address.' '.$row->District->name_th.' '.$row->amphure->name_th.' '.$row->province->name_th.' '.$row->drop_center_postcode;
        })
        ->addColumn('action', function($row){

            $btn = "<a href='/dropcenterArea/".$row->id."'>";
                $btn .= "<button type='button' id='PopoverCustomT-1' class='btn btn-success btn-sm' style='margin-right:5px;'>เขตในพื้นที่</button>";
            $btn .= "</a>";
            $btn .= "<a href='/dropcenter/".$row->id."'>";
                $btn .= "<button type='button' id='PopoverCustomT-1' class='btn btn-primary btn-sm'>ตั้งค่าใหม่</button>";
            $btn .= "</a>";

            return $btn;
        })
        ->rawColumns(['action' => 'action'])
        ->make(true);
    } 

    //  แสดงรายการข้อมูลเดิม เพื่อpreviewก่อนแก้ไข
    public function show($id)
    {
        $dropcenter = DropCenter::where('id', $id)->first();
        if($dropcenter){
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $provinces = province::get(); 
            $amphures = amphure::get(); 
            $Districts = District::get(); 
        
            return view('ManagementMenu.drop_center_create', compact(['dropcenter','employee','provinces','amphures','Districts']));
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
            'drop_center_name' => 'required',
            'drop_center_address' => 'required',
            'drop_center_sub_district' => 'required',
            'drop_center_district' => 'required',
            'drop_center_province' => 'required',
            'drop_center_postcode' => 'required',
            'drop_center_phone' => 'required',
            'drop_center_name_initial' => 'required'
        ]);

        if ($validator->fails()) {
            alert()->error('ขออภัย','กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        } 

        $dropcenter = DropCenter::find($id);
        if($dropcenter){
            $dropcenter->update([
                'drop_center_name' => $request->drop_center_name,
                'drop_center_address' => $request->drop_center_address,
                'drop_center_sub_district' => $request->drop_center_sub_district,
                'drop_center_district' => $request->drop_center_district,
                'drop_center_province' => $request->drop_center_province,
                'drop_center_postcode' => $request->drop_center_postcode,
                'drop_center_phone' => $request->drop_center_phone,
                'drop_center_name_initial' => $request->drop_center_name_initial
            ]);
            alert()->success('สำเร็จ','แก้ไขข้อมูลสำเร็จ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->to('/dropcenter_get_list/1');
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    }

    public function getListForAddEmployee() {
        $user = Auth::user();
        if($user->employee->emp_position == "เจ้าของกิจการ(Owner)"){

            $dropcenters = DropCenter::get();

        }else if($user->employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            $dropcenters = DropCenter::get();

        }else if($user->employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            $dropcenters = DropCenter::where('id', $user->employee->emp_branch_id)->get();

        }else{
            alert()->error('ขออภัย','คุณไม่มีสิทธิ์การใช้งาน')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
        // $dropcenters = DropCenter::get();
        if($dropcenters){
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $provinces = province::get();
            // dd($employee);
           
            return view('/Employees.emp_create', compact(["dropcenters",'employee','provinces']));
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    }
    
    public function find_linehallList(Request $request){
        $date = date('Y-m-d');
        $user = Auth::user();
        $TransferDropCenterBills = TransferDropCenterBill::where('transfer_sender_id',$user->emp_branch_id)
        ->where('transfer_recriver_id',$request->dcId)
        ->where('created_at', 'like', $date.'%')
        ->get();
        return json_encode($TransferDropCenterBills);
    }
    
    public function find_amphure(Request $request) {
        $provinces = province::where('id', $request->provinceid)->first();
        $amphures = amphure::where('province_id', $provinces->id)->get();
        return json_encode($amphures);
    }

    public function finddistric(Request $request) {
        $amphures = amphure::where('id', $request->districid)->first();
        $districts = District::where('amphure_id', $amphures->id)->get();
        return json_encode($districts);
    }
    
    public function findzipcode(Request $request) {
        $zipcode = District::where('id', $request->districeid)->first();
        return json_encode($zipcode);
    }
    
    public function findaddress(Request $request) {
        $provincesall = province::get();
        //ค้นหาจังหวัด
        $sql = "SELECT * FROM provinces c WHERE id IN (SELECT a.province_id FROM amphures a WHERE id IN (SELECT b.amphure_id FROM districts b WHERE b.zip_code = '$request->zipcode'))";
        $province = DB::select($sql);

        //ค้นหาอำเภอ
        $sql = "SELECT * FROM amphures a WHERE id IN (SELECT b.amphure_id FROM districts b WHERE b.zip_code = '$request->zipcode')";
        $amphures = DB::select($sql);

        //ค้นหาตำบล
        $Districts = District::where('amphure_id', $amphures[0]->id)->get();

        // dd($Districts);
        return json_encode(['province' => $province , 'amphures' => $amphures , 'Districts' => $Districts , 'provincesall' => $provincesall]);
    }

    public function getListForEditEmployee($id) {
        $user = Auth::user();
        if($user->employee->emp_position == "เจ้าของกิจการ(Owner)"){

            $dropcenters = DropCenter::get();

        }else if($user->employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            $dropcenters = DropCenter::get();

        }else if($user->employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            $dropcenters = DropCenter::where('id', $user->employee->emp_branch_id)->get();

        }else{
            alert()->error('ขออภัย','คุณไม่มีสิทธิ์การใช้งาน')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
        if($dropcenters){
            $user = User::where('employee_id',$id)->first();
            $employ = Employee::where('id',$id)->first();
            $provinces = province::get(); 
            $amphures = amphure::get(); 
            $Districts = District::get(); 
            
            $userID = Auth::user();
            $employee = Employee::where('id',$userID->employee_id)->first();
           
            // dd($employee);
            return view('/Employees.emp_edit', compact(["dropcenters","employ","user",'employee','provinces','amphures','Districts']));
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    }

    public function getDropCenterList() {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($user){
            $dropcenters = DropCenter::get();
            return view('/Transfers.tranfer_parcel_for_drop_center', compact(['dropcenters','employee']));
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        } 
    }

    public function drop_center_create() {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $provinces = province::get(); 
        return view('ManagementMenu.drop_center_create',compact('employee','provinces'));
    }

    public function dropCenterAreaGetList($id = null)
    {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $couriers = Employee::where('emp_position', 'พนักงานจัดส่งพัสดุ(Courier)')->where('emp_branch_id', $id)->get();
        return view('/ManagementMenu.drop_center_area_management', compact(['id','couriers','employee']));
    }
    
    public function dropcenterAreaDataTable(Request $request)
    {
        // dd($request->id);
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $PostCodes = PostCode::where('drop_center_id', $request->id)->get();
        return Datatables::of($PostCodes)
        ->addIndexColumn()
        ->editColumn('ampure', function($row){
            $amphurename = "";
            $i = 0;
            $Districts2s = District::where('zip_code', $row->postcode)->groupby('amphure_id')->get();
            foreach ($Districts2s as $Districts2) {
                $i++;
                $amphures = amphure::where('id', $Districts2->amphure_id)->first();
                if($i == 1){
                    $amphurename .= $amphures->name_th;
                }else{
                    $amphurename .= " , ".$amphures->name_th;
                }
            }
            return $amphurename;
        })
        ->editColumn('Districts', function($row){
            $Districts = District::where('zip_code',$row->postcode)->get(); 
            $content = "<div class='row'>";
            foreach ($Districts as $District) {
                $content .= "<div class='col-md-6'>".$District->name_th."</div>";
            }
            $content .= "</div>";

            return $content;
        })
        ->addColumn('action', function($row){

            $btn = '<a href="#" data-toggle="modal" data-target="#exampleModalCenter" onclick="courierinarea(\''.$row->postcode.'\')">';
                $btn .= '<button type="button" id="PopoverCustomT-1" class="btn btn-success btn-md" style="margin-right:5px;">Courier ที่รับผิดชอบ</button>';
            $btn .= '</a>';
            $btn .= '<a href="#" Onclick="deletedroupCenter(\''.$row->id.'\')">';
                $btn .= '<button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-md">ลบ</button>';
            $btn .= '</a>';

            return $btn;
        })
        ->rawColumns(['action' => 'action','Districts' => 'Districts'])
        ->make(true);
        // dd($PostCodes);
    }


    public function courierinarea(Request $request){
        $CourierArea = CourierArea::with('employee')->where('post_code_id',$request->zipcode)->get();
        // dd($CourierArea[0]->employee['emp_firstname']);
        return json_encode($CourierArea);
    }
    
    public function courierinarea_add(Request $request){
        $CourierArea = CourierArea::where('post_code_id',$request->zipcode)->where('employee_id',$request->courierid)->get();
        if(count($CourierArea) > 0){
            $data = '{"status":"0","msg":"ผู้ส่งซ้ำโปรดลองใหม่"}';
        }else{
            CourierArea::create([
                'post_code_id' => $request->zipcode,
                'employee_id' => $request->courierid
            ]); 

            $data = '{"status":"1","msg":"เพิ่มผู้ส่งพัสดุสำเร็จ"}';
        }
        return $data;
    }
    
    public function courierinarea_Del(Request $request){
        $CourierArea = CourierArea::find($request->id);
        if(!empty($CourierArea)){
            $CourierArea->delete();
            $data = '{"status":"1","msg":"ลบผู้ส่งพัสดุสำเร็จ"}';
        }else{
            $data = '{"status":"0","msg":"ไม่พบข้อมูล"}';
        }
        return $data;
    }
    
    public function drop_center_area_create($id = null)
    {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $dropcenters = DropCenter::find($id); 
        $provinces = province::where('id', $dropcenters->drop_center_province)->first();
        $amphures = amphure::where('province_id', $provinces->id)->get();
        // dd($amphures);
        return view('/ManagementMenu.drop_center_area_create', compact(['amphures','id','employee']));
        // dd($PostCodes);
    }
    
    public function drop_center_area_findzip(Request $request)
    {
        // $districts = District::where('amphure_id', $request->amphureid)->with('postcode')->groupby('zip_code')->get();
        $sql = "select a.*, b.drop_center_id from districts a left join post_codes b on a.zip_code = b.postcode where a.amphure_id = '$request->amphureid' group by a.zip_code";
        $districts = DB::select($sql);
        // dd($districts);
        return json_encode($districts);
    }
    
    public function drop_center_area_finddistric(Request $request)
    {
        $districts = District::where('zip_code', $request->zipcode)->get();
        return json_encode($districts);
    }
    public function dropcenterareaadd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zipcode' => 'required',
            'drop_center_id' => 'required'
        ]);

        if ($validator->fails()) {
            alert()->error('ขออภัย','เลือกข้อมูลให้ถูกต้อง')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        } 

        $postcode = PostCode::where('postcode',$request->zipcode)->get();
        if(count($postcode) == 0){
            PostCode::create([
                'postcode' => $request->zipcode,
                'drop_center_id' => $request->drop_center_id
            ]);
            $couriers = Employee::where('emp_position', 'พนักงานจัดส่งพัสดุ(Courier)')->where('emp_branch_id', $request->drop_center_id)->get();
            $id = $request->drop_center_id;
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $PostCodes = PostCode::where('drop_center_id', $id)->get();
            alert()->success('สำเร็จ','เพิ่มพื้นที่สำเร็จ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->to('/dropcenterArea/'.$employee->emp_branch_id);
            // return view('/ManagementMenu.drop_center_area_management', compact(['PostCodes','id','employee','couriers']));
        }else{
            alert()->error('ขออภัย','เขตนี้ถูกเลือกไปแล้ว')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    }
    
    public function dropcenterareadelect($id = null)
    {
        $PostCodedelect = PostCode::find($id);
        
        if(!empty($PostCodedelect)){
            $CourierAreas = CourierArea::where('post_code_id', $PostCodedelect->postcode)->get();
            // dd(count($CourierArea));
            if(count($CourierAreas) > 0){
                foreach ($CourierAreas as $key => $CourierArea) {
                    $CourierArea->delete();
                }
            }
            $id = $PostCodedelect->drop_center_id;
            $PostCodedelect->delete();
            $user = Auth::user();
            $couriers = Employee::where('emp_position', 'พนักงานจัดส่งพัสดุ(Courier)')->where('emp_branch_id', $id)->get();
            $employee = Employee::where('id',$user->employee_id)->first();
            $PostCodes = PostCode::where('drop_center_id', $id)->get();
            alert()->success('สำเร็จ','ลบพื้นที่สำเร็จ')->showConfirmButton('ตกลง','#3085d6');
            return view('/ManagementMenu.drop_center_area_management', compact(['PostCodes','id','employee','couriers']));
        }else{
            alert()->error('ขออภัย','พื้นที่นี้ถูกลบไปแล้ว')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    }

    public function find_droupcenter_Empty(Request $request){
        $sql = "SELECT a.* from drop_centers a left join area_manager_branchs b ON b.branch_id = a.id WHERE b.id IS NULL";
        $droup_centers = DB::select($sql);
        return $droup_centers;
    }
    
    public function manaArea_droupcenter_List(Request $request){
        // dd("sss");
        $AreaManagerBranchs = AreaManagerBranch::where('employee_id', $request->emp_id)->select();
        // dd($AreaManagerBranchs->get());
        return Datatables::of($AreaManagerBranchs)
        ->addIndexColumn()
        ->editColumn('branch_id',function($row){
            return $row->DropCenter->drop_center_name.'('.$row->DropCenter->drop_center_name_initial.')';
            // return $AreaManagerBranch->branch_id;
        })
        ->editColumn('create_by',function($row){
            return $row->create->emp_firstname.' '.$row->create->emp_lastname;
        })
        ->addColumn('action', function($row) {
            return '<p style="cursor:pointer; color:red; font-size:20px;" onclick="delete_manaArea_droupcenter(\''.$row->id.'\',\''.$row->employee_id.'\')"><i class="fa fa-trash" aria-hidden="true"></i></p>';
        })
        ->rawColumns(['action' => 'action'])
        ->make(true);
    }
    
    public function Add_branch_to_mana_area(Request $request){
        // dd($request->all());
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $AreaManagerBranchs = AreaManagerBranch::where('employee_id', $request->employee_id)->where('branch_id', $request->branch_id)->get();
        // dd($AreaManagerBranchs);
        if(count($AreaManagerBranchs) > 0){
            $content = '{"status":"0"}';
        }else{
            // $test = new AreaManagerBranch;
            // $test->employee_id = $request->employee_id;
            // $test->branch_id = $request->branch_id;
            // $test->create_by = $employee->id;
            // $test->save();
            // \DB::commit();

            AreaManagerBranch::insert([
                'employee_id' => $request->employee_id,
                'branch_id' => $request->branch_id,
                'create_by' => $employee->id
            ]);

            $content = '{"status":"1"}';
        }

        return $content;
    }
    
    public function delete_manaArea_droupcenter(Request $request){
        // dd($request->all());
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == 'เจ้าของกิจการ(Owner)'){
            $AreaManagerBranchs = AreaManagerBranch::find($request->id);
            if(!empty($AreaManagerBranchs)){
                $AreaManagerBranchs->delete();
                $content = '{"status":"1"}';
            }else{
                $content = '{"status":"0"}';
            }
        }else{
            $content = '{"status":"0"}';
        }
        return $content;
    }

}
