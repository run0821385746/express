<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Admin;
use App\Model\Employee;
use App\Model\CourierArea;
use App\Model\Permission;
use App\Model\User;
use App\Model\DropCenter;
use App\Model\CourierLogin;
use App\Model\LoginStartDay;
use App\Model\CourierRequestPassword;
use App\Model\province;
use App\Model\amphure;
use App\Model\District;
use Validator;
use Auth;
use DB;
use DataTables;
  
class EmployeeController extends Controller
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
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'emp_firstname' => 'required',
            'emp_lastname' => 'required',
            'emp_address' => 'required',
            'emp_sub_district' => 'required',  
            'emp_district' => 'required',
            'emp_province' => 'required',
            'emp_postcode' => 'required',
            'emp_phone' => 'required',
            'email' => 'required',
            'username' => 'required',
            'password' => 'required',
            'emp_branch_id' => 'required'
  
        ]);
        if($validator->fails()) {
            alert()->error('ขออภัย', 'กรอกข้อมูลให้ครบ')->showConfirmButton("ตกลง","#3085d6");
            return redirect()->back();
        }  

        $Users = User::where('email', $request->email)->orwhere('username', $request->username)->first();
        if(!empty($Users)){
            alert()->error('ขออภัย', 'email หรือ username ซ้ำ')->showConfirmButton("ตกลง","#3085d6");
            return redirect()->back();
        }

        $employee = Employee::create([
            'emp_firstname' => $request->emp_firstname,
            'emp_lastname' => $request->emp_lastname,
            'emp_address' => $request->emp_address,
            'emp_sub_district' => $request->emp_sub_district,
            'emp_district' => $request->emp_district,
            'emp_province' => $request->emp_province,
            'emp_postcode' => $request->emp_postcode,
            'emp_phone' => $request->emp_phone,
            'emp_position' => $request->emp_position,
            'emp_status' => true,
            'emp_branch_id' => $request->emp_branch_id
        ]);

        $usertable = [
            'name' => $request->emp_firstname,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'employee_id' => $employee->id,
            'emp_branch_id' => $request->emp_branch_id
        ];
        DB::table('users')->insert($usertable);
        // User::create([
        //     'name' => $request->emp_firstname,
        //     'email' => $request->email,
        //     'username' => $request->username,
        //     'password' => bcrypt($request->password),
        //     'employee_id' => $request->emp_branch_id,
        //     'emp_branch_id' => $request->emp_branch_id
        // ]);

        if($request->emp_position == "เจ้าของกิจการ(Owner)"){
            Permission::create([
                'emp_id' => $employee->id,
                'daily_summaries_menu' => '0', 
                'parcel_care_menu' => '0', 
                'receive_parcel_menu' => '0', 
                'all_parcel_menu' => '0', 
                'parcel_cls_menu' => '0', 
                'parcel_send_menu' => '0', 
                'parcel_call_recive_menu' => '0', 
                'recive_parcel_from_dc_menu' => '0', 
                'orther_report_menu' => '0',
                'customer_menu' => '0',
                'employ_menu' => '0',
                'permiss_menu' => '0',
                'dropcenter_menu' => '0',
                'orther_sale_menu' => '0',
                'service_price_menu' => '0',
                'parcel_type_menu' => '0',
                'permission_status' => true,
                'branch_id' => $request->emp_branch_id,
                'update_by' => $user->id
            ]);
        }else if($request->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){
            Permission::create([
                'emp_id' => $employee->id,
                'daily_summaries_menu' => '0', 
                'parcel_care_menu' => '0', 
                'receive_parcel_menu' => '0', 
                'all_parcel_menu' => '0', 
                'parcel_cls_menu' => '0', 
                'parcel_send_menu' => '0', 
                'parcel_call_recive_menu' => '0', 
                'recive_parcel_from_dc_menu' => '0', 
                'orther_report_menu' => '0',
                'customer_menu' => '0',
                'employ_menu' => '0',
                'permiss_menu' => '0',
                'dropcenter_menu' => '0',
                'orther_sale_menu' => '1',
                'service_price_menu' => '1',
                'parcel_type_menu' => '1',
                'permission_status' => true,
                'branch_id' => $request->emp_branch_id,
                'update_by' => $user->id
            ]);
        }else if($request->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){
            Permission::create([
                'emp_id' => $employee->id,
                'daily_summaries_menu' => '0', 
                'parcel_care_menu' => '0', 
                'receive_parcel_menu' => '0', 
                'all_parcel_menu' => '0', 
                'parcel_cls_menu' => '0', 
                'parcel_send_menu' => '0', 
                'parcel_call_recive_menu' => '0', 
                'recive_parcel_from_dc_menu' => '0', 
                'orther_report_menu' => '0',
                'customer_menu' => '0',
                'employ_menu' => '0',
                'permiss_menu' => '0',
                'dropcenter_menu' => '1',
                'orther_sale_menu' => '1',
                'service_price_menu' => '1',
                'parcel_type_menu' => '1',
                'permission_status' => true,
                'branch_id' => $request->emp_branch_id,
                'update_by' => $user->id
            ]);
        }else if($request->emp_position == "พนักงานหน้าร้าน(Admin)"){
            Permission::create([
                'emp_id' => $employee->id,
                'daily_summaries_menu' => '0', 
                'parcel_care_menu' => '0', 
                'receive_parcel_menu' => '0', 
                'all_parcel_menu' => '0', 
                'parcel_cls_menu' => '0', 
                'parcel_send_menu' => '0', 
                'parcel_call_recive_menu' => '0', 
                'recive_parcel_from_dc_menu' => '0', 
                'orther_report_menu' => '0',
                'customer_menu' => '1',
                'employ_menu' => '1',
                'permiss_menu' => '1',
                'dropcenter_menu' => '1',
                'orther_sale_menu' => '1',
                'service_price_menu' => '1',
                'parcel_type_menu' => '1',
                'permission_status' => true,
                'branch_id' => $request->emp_branch_id,
                'update_by' => $user->id
            ]);
        }else{
            Permission::create([
                'emp_id' => $employee->id,
                'daily_summaries_menu' => '1', 
                'parcel_care_menu' => '1', 
                'receive_parcel_menu' => '1', 
                'all_parcel_menu' => '1', 
                'parcel_cls_menu' => '1', 
                'parcel_send_menu' => '1', 
                'parcel_call_recive_menu' => '1', 
                'recive_parcel_from_dc_menu' => '1', 
                'orther_report_menu' => '1',
                'customer_menu' => '1',
                'employ_menu' => '1',
                'permiss_menu' => '1',
                'dropcenter_menu' => '1',
                'orther_sale_menu' => '1',
                'service_price_menu' => '1',
                'parcel_type_menu' => '1',
                'permission_status' => true,
                'branch_id' => $request->emp_branch_id,
                'update_by' => $user->id
            ]);
        }
        
        
        $Admin = [
            'name' => $request->emp_firstname,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'employee_id' => $employee->id,
            'emp_branch_id' => $request->emp_branch_id

        ];
        DB::table('admins')->insert($Admin);

        alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
        return redirect()->to('/employee_list/'.$user->employee->emp_branch_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response  
     */

    public function editProfile()  // get emp list by branch_id for rander at emp_management
    {
        $user = Auth::user();
        if(!empty($user)){
            $employee = Employee::where('id',$user->employee_id)->first();
            if($employee->emp_position == 'เจ้าของกิจการ(Owner)' || $employee->emp_position == 'ผู้จัดการเขตพื้นที่(Area Manager)'){
                $dropcenters = DropCenter::get();
            }else{
                $dropcenters = DropCenter::where('id', $user->employee->emp_branch_id)->get();
            }
            $employ = Employee::where('id',$user->employee_id)->first();
            $provinces = province::get(); 
            $amphures = amphure::get(); 
            $Districts = District::get(); 
            $editProfile = 0;
            return view('/Employees.emp_edit', compact(["dropcenters","employ","user",'employee','provinces','amphures','Districts','editProfile']));
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    } 

    public function getList($branch_id = null)  // get emp list by branch_id for rander at emp_management
    {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == "เจ้าของกิจการ(Owner)"){
            $employees = Employee::get();
            // dd($permissions);
        }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            $employees = Employee::where("emp_position", "!=", "เจ้าของกิจการ(Owner)")->whereIn('emp_branch_id', [1, 2, 3])->get();

        }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            $employees = Employee::where('emp_branch_id', $branch_id)->get();

        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
        // $employees = Employee::where('emp_branch_id', $branch_id)->get();   //findorfiled , first คนเดียว
        if($employees){
           
            return view('Employees.emp_management',compact(['employees','employee']));
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }           
    
    public function requerest_password($branch_id = null)  // get emp list by branch_id for rander at emp_management
    {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == "เจ้าของกิจการ(Owner)"){
            // $CourierLogins = CourierLogin::get();
            // dd($permissions);
        }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            // $CourierLogins = CourierLogin::get();

        }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            // $CourierLogins = CourierLogin::where('branch_id', $branch_id)->get();

        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
        
        return view('Employees.requerest_password_management',compact(['employee']));
    }   

    public function requerest_password_datatable(Request $request)  // get emp list by branch_id for rander at emp_management
    {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == "เจ้าของกิจการ(Owner)"){
            $CourierRequestPassword = CourierRequestPassword::orderby('created_at', 'DESC')->get();
            // dd($permissions);
        }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            $CourierRequestPassword = CourierRequestPassword::orderby('created_at', 'DESC')->get();

        }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            $CourierRequestPassword = CourierRequestPassword::where('emp_branch_id', $employee->emp_branch_id)->orderby('created_at', 'DESC')->get();

        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
        if($CourierRequestPassword){
            return Datatables::of($CourierRequestPassword)
            ->addIndexColumn()
            ->editColumn('employee_id',function($row){
                return $row->employee->emp_firstname.' '.$row->employee->emp_lastname;
            })
            ->editColumn('create_at', function($row){
                $date_time = date('d/m/Y H:i:s', strtotime($row->created_at));
                return $date_time;
            })
            ->editColumn('status', function($row){
                if($row->status == 'new'){
                    $type = "<span style='color:blue;'>คำขอใหม่</span>";
                }else{
                    $type = "<span style='color:green;'>ดำเนินการแล้ว</span>";
                }
                return $type;
            })
            ->addColumn('action', function($row){
                if($row->status == 'new'){
                    $btn = "<a href='/reset_new_password/".$row->id."'><button class='btn btn-primary btn-sm'>Reset</button></a>";
                }else{
                    $btn = "<button class='btn btn-success btn-sm'>ดำเนินการสำเร็จ</button>";
                }
                return $btn;
            })
            ->rawColumns(['action' => 'action','status' => 'status'])
            ->make(true);
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }    

    public function reset_new_password($id = null){
        if($id){
            $CourierRequestPassword = CourierRequestPassword::find($id);
            if($CourierRequestPassword){
                $User = User::where('employee_id', $CourierRequestPassword->employee_id)->first();
                $Admin = Admin::where('employee_id', $CourierRequestPassword->employee_id)->first();
                
                $User->update([
                    'password' => bcrypt('123456')
                ]);
                
                $Admin->update([
                    'password' => bcrypt('123456')
                ]);
                
                $CourierRequestPassword->update([
                    'status' => 'done'
                ]);

                alert()->success('สำเร็จ','รหัสผ่านถูก Reset สำเร็จ')->showConfirmButton('ตกลง','#3085d6');
                return redirect()->back();
            }else{
                alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
                return redirect()->back();
            }
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
    }

    public function courier_login_his($branch_id = null)  // get emp list by branch_id for rander at emp_management
    {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == "เจ้าของกิจการ(Owner)"){
            // $CourierLogins = CourierLogin::get();
            // dd($permissions);
        }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            // $CourierLogins = CourierLogin::get();

        }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            // $CourierLogins = CourierLogin::where('branch_id', $branch_id)->get();

        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
        
        return view('Employees.courier_login_management',compact(['employee']));
    }      
    
    public function courier_login_his_datatable(Request $request)  // get emp list by branch_id for rander at emp_management
    {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == "เจ้าของกิจการ(Owner)"){
            $CourierLogins = CourierLogin::orderby('created_at', 'DESC')->get();
            // dd($permissions);
        }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            $CourierLogins = CourierLogin::orderby('created_at', 'DESC')->get();

        }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            $CourierLogins = CourierLogin::where('branch_id', $employee->emp_branch_id)->orderby('created_at', 'DESC')->get();

        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
        if($CourierLogins){
            return Datatables::of($CourierLogins)
            ->addIndexColumn()
            ->editColumn('employee_id',function($row){
                return $row->employee->emp_firstname.' '.$row->employee->emp_lastname;
            })
            ->editColumn('login_type',function($row){
                if($row->login_type == 'courier'){
                    $type = "Courier";
                }else{
                    $type = "Line Haull";
                }
                return $type;
            })
            ->editColumn('updated_at', function($row){
                $date_time = date('d/m/Y H:i:s', strtotime($row->updated_at));
                return $date_time;
            })
            ->editColumn('login_status', function($row){
                if($row->login_status == '0'){
                    $type = "<span style='color:gray;'>Login ไม่สำเร็จ</span>";
                }else{
                    $type = "<span style='color:green;'>Login สำเร็จ</span>";
                }
                return $type;
            })
            ->editColumn('lat_long', function($row){
                return "<a href='https://www.google.com/maps/dir//".$row->lat_long."/@".$row->lat_long."/data=!4m2!4m1!3e0' target='_blank'><button class='btn btn-primary brn-sm')\">View</button></a>";
            })
            ->addColumn('action', function($row){
                if($row->login_status == '0'){
                    $photo = "<span style='color:gray;'>Login ไม่สำเร็จ</span>";
                }else{
                    $photo = '<div><button class="btn btn-warning btn-md" id="btn'.$row->id.'" OnClick="ShowViewPhoto(\''.$row->id.'\',\'btn\',\'photo\')">View</button></div><img id="photo'.$row->id.'" style="display:none;" src="data:image/jpeg;base64,'.$row->courier_login_image.'" width="100%" />';
                }
                return $photo;
            })
            ->rawColumns(['action' => 'action','login_status' => 'login_status','lat_long' => 'lat_long'])
            ->make(true);
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }        
    
    public function courier_login_stampDay_datatable(Request $request)  // get emp list by branch_id for rander at emp_management
    {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == "เจ้าของกิจการ(Owner)"){
            $LoginStartDays = LoginStartDay::whereDate('created_at', '=', $request->date)->orderby('created_at', 'DESC')->get();
            // dd($permissions);
        }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            $LoginStartDays = LoginStartDay::whereDate('created_at', '=', $request->date)->orderby('created_at', 'DESC')->get();

        }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            $LoginStartDays = LoginStartDay::where('branch_id', $employee->emp_branch_id)->whereDate('created_at', '=', $request->date)->orderby('created_at', 'DESC')->get();

        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }
        if($LoginStartDays){
            return Datatables::of($LoginStartDays)
            ->addIndexColumn()
            ->editColumn('employee_id',function($row){
                return $row->Employee->emp_firstname.' '.$row->Employee->emp_lastname;
            })
            ->editColumn('login_type',function($row) use($request){
                $employee = Employee::where('id',$row->employee_id)->first();
                if($employee->emp_position == 'พนักงานจัดส่งพัสดุ(Courier)'){
                    $type = "Courier";
                }else if($employee->emp_position == 'พนักงานส่งพัสดุ(Line Haul)'){
                    $type = "Line Haull";
                }
                    return $type;
            })
            ->editColumn('login_time', function($row){
                $date_time = date('d/m/Y H:i:s', strtotime($row->login_time));
                return $date_time;
            })
            ->editColumn('login_lat_long', function($row) {
                if($row->login_lat_long !== ""){
                    return "<a href='https://www.google.com/maps/dir//".$row->login_lat_long."/@".$row->login_lat_long."/data=!4m2!4m1!3e0' target='_blank'><button class='btn btn-primary brn-sm')\">View</button></a>";
                }else{
                    $photo = '<button class="btn btn-light btn-md">ไม่พบข้อมูล</button>';
                    return $photo;
                }
            })
            ->addColumn('login_img', function($row){
                if($row->login_img !== ''){
                    $photo = '<div><button class="btn btn-warning btn-md" id="btnlogin'.$row->id.'" OnClick="ShowViewPhoto(\''.$row->id.'\',\'btnlogin\',\'photologin\')">View</button></div><img id="photologin'.$row->id.'" style="display:none;" src="data:image/jpeg;base64,'.$row->login_img.'" width="100%" />';
                }else{
                    $photo = '<button class="btn btn-light btn-md">ไม่พบข้อมูล</button>';
                }
                return $photo;
            })
            ->editColumn('logout_time', function($row){
                if($row->logout_time == '0000-00-00 00:00:00'){
                    $date_time = 'ยังไม่ลงเวลาออก';
                }else{
                    $date_time = date('d/m/Y H:i:s', strtotime($row->logout_time));
                }
                return $date_time;
            })
            ->editColumn('logout_lat_long', function($row) {
                if($row->logout_lat_long !== ''){
                    return "<a href='https://www.google.com/maps/dir//".$row->logout_lat_long."/@".$row->logout_lat_long."/data=!4m2!4m1!3e0' target='_blank'><button class='btn btn-primary brn-sm')\">View</button></a>";
                }else{
                    $photo = '<button class="btn btn-light btn-md">ไม่พบข้อมูล</button>';
                }
                
            })
            ->addColumn('logout_img', function($row){
                if($row->logout_img !== ''){
                    $photo = '<div><button class="btn btn-warning btn-md" id="btnlogout'.$row->id.'" OnClick="ShowViewPhoto(\''.$row->id.'\',\'btnlogout\',\'photologout\')">View</button></div><img id="photologout'.$row->id.'" style="display:none;" src="data:image/jpeg;base64,'.$row->logout_img.'" width="100%" />';
                }else{
                    $photo = '<button class="btn btn-light btn-md">ไม่พบข้อมูล</button>';
                }
                return $photo;
            })
            ->rawColumns(['login_lat_long' => 'login_lat_long','login_img' => 'login_img','logout_lat_long' => 'logout_lat_long','logout_img' => 'logout_img'])
            ->make(true);
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }        

    public function show($id)  //get emp detail by id for rander at emp_create 
    {
        $employee = Employee::where('id', $id)->first();   //findorfiled , first คนเดียว
         if($employee){
            return view('Employees.emp_create',compact('employee'));
         }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
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
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'emp_firstname' => 'required',
            'emp_lastname' => 'required',
            'emp_address' => 'required',
            'emp_sub_district' => 'required',  
            'emp_district' => 'required',
            'emp_province' => 'required',
            'emp_postcode' => 'required',
            'emp_phone' => 'required',
            'emp_position' => 'required',
            'emp_branch_id' => 'required',
            'emp_status' => 'required',
            'username' => 'required'
        ]);
        if($validator->fails()) {
            alert()->error('ขออภัย', 'กรอกข้อมูลให้ครบ')->showConfirmButton("ตกลง","#3085d6");
            return redirect()->back();
        }  
       
        $employee = Employee::find($id);
        
        if($employee){
            $employee->update([
                'emp_firstname' => $request->emp_firstname,
                'emp_lastname' => $request->emp_lastname,
                'emp_address' => $request->emp_address,
                'emp_sub_district' => $request->emp_sub_district,
                'emp_district' => $request->emp_district,
                'emp_province' => $request->emp_province,
                'emp_postcode' => $request->emp_postcode,
                'emp_phone' => $request->emp_phone,
                'emp_position' => $request->emp_position,
                'emp_branch_id' => $request->emp_branch_id,
                'emp_status' => $request->emp_status
            ]);

            $user = User::where('employee_id',$id)->first();
            if($user){
                if($request->password != ""){
                    $user->update([
                        'name' => $request->emp_firstname,
                        'email' => $request->username,
                        'password' => bcrypt($request->password),
                        'emp_branch_id' => $request->emp_branch_id
                    ]);
                }else{
                    $user->update([
                        'name' => $request->emp_firstname,
                        'email' => $request->username,
                        'emp_branch_id' => $request->emp_branch_id
                    ]);
                }
            }

            $admin = Admin::where('employee_id',$id)->first();
            if($admin){
                if($request->password != ""){
                    $admin->update([
                        'name' => $request->emp_firstname,
                        'email' => $request->username,
                        'username' => $request->username,
                        'password' => bcrypt($request->password),
                        'emp_branch_id' => $request->emp_branch_id
                    ]);
                }else{
                    $admin->update([
                        'name' => $request->emp_firstname,
                        'email' => $request->username,
                        'username' => $request->username,
                        'emp_branch_id' => $request->emp_branch_id
                    ]);
                }
            }

            $Permission = Permission::where('emp_id',$id)->first();
            if($request->emp_position == "เจ้าของกิจการ(Owner)"){
                $Permission->update([
                    'emp_id' => $employee->id,
                    'daily_summaries_menu' => '0', 
                    'parcel_care_menu' => '0', 
                    'receive_parcel_menu' => '0', 
                    'all_parcel_menu' => '0', 
                    'parcel_cls_menu' => '0', 
                    'parcel_send_menu' => '0', 
                    'parcel_call_recive_menu' => '0', 
                    'recive_parcel_from_dc_menu' => '0', 
                    'orther_report_menu' => '0',
                    'customer_menu' => '0',
                    'employ_menu' => '0',
                    'permiss_menu' => '0',
                    'dropcenter_menu' => '0',
                    'orther_sale_menu' => '0',
                    'service_price_menu' => '0',
                    'parcel_type_menu' => '0',
                    'permission_status' => true,
                    'branch_id' => $request->emp_branch_id,
                    'update_by' => $user->id
                ]);
            }else if($request->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){
                Permission::create([
                    'emp_id' => $employee->id,
                    'daily_summaries_menu' => '0', 
                    'parcel_care_menu' => '0', 
                    'receive_parcel_menu' => '0', 
                    'all_parcel_menu' => '0', 
                    'parcel_cls_menu' => '0', 
                    'parcel_send_menu' => '0', 
                    'parcel_call_recive_menu' => '0', 
                    'recive_parcel_from_dc_menu' => '0', 
                    'orther_report_menu' => '0',
                    'customer_menu' => '0',
                    'employ_menu' => '0',
                    'permiss_menu' => '0',
                    'dropcenter_menu' => '0',
                    'orther_sale_menu' => '1',
                    'service_price_menu' => '1',
                    'parcel_type_menu' => '1',
                    'permission_status' => true,
                    'branch_id' => $request->emp_branch_id,
                    'update_by' => $user->id
                ]);
            }else if($request->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){
                $Permission->update([
                    'emp_id' => $employee->id,
                    'daily_summaries_menu' => '0', 
                    'parcel_care_menu' => '0', 
                    'receive_parcel_menu' => '0', 
                    'all_parcel_menu' => '0', 
                    'parcel_cls_menu' => '0', 
                    'parcel_send_menu' => '0', 
                    'parcel_call_recive_menu' => '0', 
                    'recive_parcel_from_dc_menu' => '0', 
                    'orther_report_menu' => '0',
                    'customer_menu' => '0',
                    'employ_menu' => '0',
                    'permiss_menu' => '0',
                    'dropcenter_menu' => '0',
                    'orther_sale_menu' => '1',
                    'service_price_menu' => '1',
                    'parcel_type_menu' => '1',
                    'permission_status' => true,
                    'branch_id' => $request->emp_branch_id,
                    'update_by' => $user->id
                ]);
            }else if($request->emp_position == "พนักงานหน้าร้าน(Admin)"){
                $Permission->update([
                    'emp_id' => $employee->id,
                    'daily_summaries_menu' => '0', 
                    'parcel_care_menu' => '0', 
                    'receive_parcel_menu' => '0', 
                    'all_parcel_menu' => '0', 
                    'parcel_cls_menu' => '0', 
                    'parcel_send_menu' => '0', 
                    'parcel_call_recive_menu' => '0', 
                    'recive_parcel_from_dc_menu' => '0', 
                    'orther_report_menu' => '0',
                    'customer_menu' => '1',
                    'employ_menu' => '1',
                    'permiss_menu' => '1',
                    'dropcenter_menu' => '1',
                    'orther_sale_menu' => '1',
                    'service_price_menu' => '1',
                    'parcel_type_menu' => '1',
                    'permission_status' => true,
                    'branch_id' => $request->emp_branch_id,
                    'update_by' => $user->id
                ]);
            }else{
                $Permission->update([
                    'emp_id' => $employee->id,
                    'daily_summaries_menu' => '1', 
                    'parcel_care_menu' => '1', 
                    'receive_parcel_menu' => '1', 
                    'all_parcel_menu' => '1', 
                    'parcel_cls_menu' => '1', 
                    'parcel_send_menu' => '1', 
                    'parcel_call_recive_menu' => '1', 
                    'recive_parcel_from_dc_menu' => '1', 
                    'orther_report_menu' => '1',
                    'customer_menu' => '1',
                    'employ_menu' => '1',
                    'permiss_menu' => '1',
                    'dropcenter_menu' => '1',
                    'orther_sale_menu' => '1',
                    'service_price_menu' => '1',
                    'parcel_type_menu' => '1',
                    'permission_status' => true,
                    'branch_id' => $request->emp_branch_id,
                    'update_by' => $user->id
                ]);
            }
            // $Permission->update([
            //     'branch_id' => $request->emp_branch_id
            // ]);
            
            $user = Auth::user();
            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->to('/employee_list/'.$user->employee->emp_branch_id);
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function getCurierList($id = null) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        return view('Transfers.transfer_parcel_for_courier',compact(['id','employee']));
    }
    
    public function Employee_request_password(Request $request) {
        // dd($request->all());
        if ($request->email) {
            $user = User::where('email',$request->email)->first();
            if($user){
                $Admin = Admin::where('employee_id', $user->employee_id)->first();
                
                $user->update([
                    'password' => bcrypt('123456')
                ]);
                
                $Admin->update([
                    'password' => bcrypt('123456')
                ]);

                alert()->success('สำเร็จ','รีเซ็ตรหัสผ่านเป็นค่าเริ่มต้นแล้ว')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->to('/login');
            }else{
                alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->back();
            }
        }else{
            alert()->error('ขออภัย', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }
    
    public function otp_submit_password(Request $request) {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'otp_submit' => 'required'
        ]);
        if($validator->fails()) {
            return '{"status":"0","msg":"กรุณากรอกรหัส OTP"}';
        }  
        if(hash('sha256',$request->otp_submit) == $request->otp){
            return '{"status":"1","msg":"รหัสยืนยัน OTP ถูกต้อง"}';
        }else{
            return '{"status":"0","msg":"รหัสยืนยัน OTP ไม่ถูกต้อง"}';
        }

    }
    public function get_otp_for_reset(Request $request) {
        // dd($request->all());
        // dd("sss");
        if ($request->email) {
            $user = User::where('email',$request->email)->first();
            if($user){
                $otp = rand(100000,999999);
                $strTo = $user->email;
                $strSubject = "=?utf-8?B?".base64_encode("SERVICE EXPRESS")."?=";
                $strHeader = "MIME-Version: 1.0' . \r\n";
                $strHeader .= "Content-type: text/html; charset=utf-8\r\n"; 
                $strHeader .= "From: SERVICE-EXPRESS<Admin@Serviceexpress.co.th>\r\nReply-To: Admin@Serviceexpress.co.th";
                $strMessage = "รหัสความปลอดภัยในการกู้คืนบัญชีผู้ใช้ของคุณคือ ".$otp." โปรดยืนยันภายใน 15นาที";

                $flgSend = @mail($strTo,$strSubject,$strMessage,$strHeader);
                if($flgSend)
                {
                    return '{"status":"1","msg":"'.hash('sha256',$otp).'"}';
                }else{
                    return '{"status":"0","msg":"ส่ง OTP ไม่สำเร็จ โปรดลองใหม่อีกครั้ง"}';
                }
            }else{
                return '{"status":"0","msg":"ไม่พบผู้ใช้ โปรดลองใหม่อีกครั้ง"}';
            }
        }else{
            return '{"status":"0","msg":"เกิดข้อผิดพลาด กรุณารีเฟรชเพจและลองใหม่ !!"}';
        }
    }

    public function getCurierListDatatable(Request $request) {
        $courierList = Employee::where('emp_position', "พนักงานจัดส่งพัสดุ(Courier)")->where('emp_branch_id', $request->id)->where('emp_status', '1')->get();
        // dd($courierList);
        return Datatables::of($courierList)
            ->addIndexColumn()
            ->editColumn('emp_firstname',function($row){
                return $emp_firstname = $row->emp_firstname.' '.$row->emp_lastname;
            })
            ->editColumn('emp_status',function($row){
                if($row->courierstatus == '' || $row->courierstatus == '1'){
                    return "<div class='badge badge-secondary'>Offline</div>";
                }else{
                    return "<div class='badge badge-warning'>Online</div>";
                }
            })
            ->editColumn('area',function($row){
                $CourierAreas = CourierArea::where('employee_id', $row->id)->get();
                $area = "";
                foreach ($CourierAreas as $key => $CourierAreas) {
                    if($key == 0){
                        $area .= $CourierAreas->post_code_id;
                    }else{
                        $area .= ' , '.$CourierAreas->post_code_id;
                    }
                }
                return $area;
            })
            ->editColumn('cod',function($row){
                return "อนุญาติ";
            })
            ->addColumn('action', function($row) {
                if($row->courierstatus == '' || $row->courierstatus == '1'){
                    $btn = "<button type='button' id='PopoverCustomT-1' class='btn btn-light btn-sm'>ทำจ่ายพัสดุ</button>";
                }else{
                    $btn = "<a href='/getTransferByCourier/$row->id'>";
                        $btn .= "<button type='button' id='PopoverCustomT-1' class='btn btn-primary btn-sm'>ทำจ่ายพัสดุ</button>";
                    $btn .= "</a>";
                }
                return $btn;
            })
            ->rawColumns(['action' => 'action','emp_status' => 'emp_status'])
            ->make(true);
        // return view('Transfers.transfer_parcel_for_courier',compact(['id']));
    }
    
    public function update_img_profile(Request $request) {
        // dd($request->all());
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $employee->update([
            'emp_image' => $request->new_photo
        ]);

        alert()->success('สำเร็จ','แก้ไขรูปภาพสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
        return redirect()->back();
    }
}
  