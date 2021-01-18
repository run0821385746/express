<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Customer;
use App\Model\Booking;
use App\Model\Employee;
use App\Model\PostCode;
use App\Model\province;
use App\Model\amphure;
use App\Model\District;
use App\Model\CustomerCod;
use Storage;
use DB;
use Validator;
use Auth;
use DataTables;

class CustomerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */  

    //  เพิ่มข้อมูลลูกค้าใหม่ เมื่อบันทึกแล้วจะส่งข้อมูลไปหน้าสร้างใบรับพร้อมส่งข้อมูลลูกค้าใหม่ไปแสดงที่หน้านั้น 
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
            $flag = $request->flag; 
            if($flag=="addInManagement") {
                alert()->error('ขออภัย', 'กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->to('/get_customer_list/1');
                
            }else{
                alert()->error('ขออภัย', 'กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->to('/input');
            }
        }
        $customer = Customer::create([
            'cust_name' => $request->cust_name,
            'cust_address' => $request->cust_address,
            'cust_sub_district' => $request->cust_sub_district,
            'cust_district' => $request->cust_district,
            'cust_province' => $request->cust_province,
            'cust_postcode' => $request->cust_postcode,
            'cust_phone' => $request->cust_phone,
            'cust_status' => true
        ]);
  
        // ทำการ update sender id ใน  booking id นั้นต่อเลย 
        $sender_id = $customer->id;
        $booking_id = $request->booking_id;

        if($request->tracking_id){
            alert()->success('สำเร็จ', 'บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->to('/receiver_search_receive/'.$request->tracking_id);
        }else{
        
            if($booking_id) {
                $booking = Booking::find($request->booking_id);
                if($booking) {
                    $booking->update([
                        'booking_no' => '202004120000',
                        'booking_branch_id' => '1',
                        'booking_sender_id' => $sender_id,
                        'booking_type' => '-',
                        'booking_status' => 'doing2',
                        'create_by' => '1'
                    ]);

                    alert()->success('สำเร็จ', 'บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
                    return redirect()->to('/input/'.$customer->id);

                }else{
                    //กรณีที่มี booking id แต่ update sender id ไม่ได้ 
                    alert()->error('ขออภัย', 'กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง', '#3085d6');
                    return redirect()->to('/input');
                }
            
            }else{
                //กรณีที่ไม่มี booking id มา จะข้ามขั้นตอน row68 มาแสดง alert นี้เลย
                $flag = $request->flag; 
                if($flag=="addInManagement") { //ตรงนี้เช็ค  flag  ว่ามาจาก หน้าเพิ่มจาก management หรือไม่
                    alert()->success('สำเร็จ', 'บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
                    return redirect()->to('/get_customer_list/1');
        
                }else{
                    alert()->success('สำเร็จ', 'บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
                    return redirect()->to('/input/'.$customer->id);
                }
            }
        }
    }   

    // ฟังก์ชั่นสำหรับแสดงรายการรับตอนสร้างใบรับ
    public function inputIndex($id = null){
        if($id){
            $customer = Customer::find($id);
        }else{
            $customer = null;  
        }
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        return view('input',compact(['customer','employee']));
    } 

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */   
      
    //  แสดงรายการลูกค้าทั้งหมดและแสดงให้หน้าลิสต์   //used
    public function sender_search(Request $request) {
        $validator = Validator::make($request->all(), [
            'search_phone' => 'required'
        ]);
        if ($validator->fails()) {
            alert()->error('ขออภัย', 'กรุณากรอกเบอร์โทร')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        } 
        $customers = Customer::where('cust_phone', 'like', '%'. $request->search_phone.'%')->get();
        $search_phone = $request->search_phone;
        $booking_id = $request->booking_id;
        $provinces = province::get();
        $Districts = District::get();
        $sql = "SELECT a.id, a.name_th AS tname, a.zip_code, b.name_th AS aname, c.name_th AS pname  FROM districts a LEFT JOIN amphures b ON a.amphure_id = b.id LEFT JOIN provinces c ON b.province_id = c.id";
        $Districts = DB::select($sql);
        $user = Auth::user();
        // dd($user);
        $employee = Employee::where('id',$user->employee_id)->first();
        return view('Customers.customer_search',compact(['customers','search_phone','booking_id','employee','user','provinces','Districts']));
    }  

    // เลือกข้อมูลผู้ส่งพัสดุ 1:1
    public function show($id)
    {
        $customer = Customer::find($id);   
        if($customer){
            return view('input',compact('customer'));
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function customer_selected_receive(Request $request)
    {
        $customer = Customer::find($id);   
        if($customer){
            return view('input',compact('customer'));
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
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
            return redirect()->to('/get_customer_list/1');
        }
        $customer = Customer::find($id);
        if($customer){
            $customer->update([
                'cust_name' => $request->cust_name,
                'cust_address' => $request->cust_address,
                'cust_sub_district' => $request->cust_sub_district,
                'cust_district' => $request->cust_district,
                'cust_province' => $request->cust_province,
                'cust_postcode' => $request->cust_postcode,
                'cust_phone' => $request->cust_phone,
                'cust_status' => true
            ]);
            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->to('/get_customer_list/1');
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function showCustomerDataWhenUpdateTrackingSuccess(Request $request) {
       
         $customer = Customer::find($request->id);   
         if($customer){
             return view('Receives.receive_add_parcel',compact('customer'));
         }else{
             alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
             return redirect()->back();
         }
    }

    public function disabled_cod_account($id) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();     
        $CustomerCod = CustomerCod::find($id);
        $CustomerCod->update([
            'cod_status' => 0
        ]);
        return redirect()->back();
    }
    
    public function enable_cod_account($id) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();     
        $CustomerCod = CustomerCod::find($id);
        $CustomerCod->update([
            'cod_status' => 1
        ]);
        return redirect()->back();
    }

    public function getCustomerList($id = null) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();     
        return view('Customers.customer_list',compact(['employee']));
    }
    
    public function customerListDataTable(Request $request) {
        $customers = Customer::get();
        return Datatables::of($customers)
        ->addIndexColumn()
        ->editColumn('cust_address', function($row){
            return $row->cust_address.' '.$row->District->name_th.' '.$row->amphure->name_th.' '.$row->province->name_th;
        })
        ->editColumn('cust_status', function($row){
            if($row->cust_status == '1'){
                $status = 'ปกติ';
            }else{
                $status = 'ยกเลิก';
            }
            return $status;
        })
        ->editColumn('cust_cod_register_status', function($row){
            if($row->cust_cod_register_status == null){
                $CODbtn = '<a href="#" onclick="addCustomerCOD(\''.$row->id.'\',\''.$row->cust_phone.'\',\'\')">';
                    $CODbtn .= '<button type="button" id="PopoverCustomT-1" class="btn btn-secondary btn-sm">Register</button>';
                $CODbtn .= "</a>";
            }else{
                $CODbtn = '<div class="dropdown show">';
                    if($row->CustomerCod->cod_status == '1'){
                        $CODbtn .= '<a class="btn btn-success btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    }else{
                        $CODbtn .= '<a class="btn btn-danger btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    }
                        $CODbtn .= "Action";
                    $CODbtn .= '</a>';
                    $CODbtn .= '<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">';
                        $CODbtn .= '<a class="dropdown-item" href="#" onclick="addCustomerCOD(\''.$row->id.'\',\''.$row->cust_phone.'\',\''.$row->cust_cod_register_status.'\')">View/Edit</a>';
                        if($row->CustomerCod->cod_status == '1'){
                            $CODbtn .= '<a class="dropdown-item" href="/disabled_cod_account/'.$row->cust_cod_register_status.'">Disabled COD account</a>';
                        }else{
                            $CODbtn .= '<a class="dropdown-item" href="/enable_cod_account/'.$row->cust_cod_register_status.'">Enable COD account</a>';
                        }
                    $CODbtn .= '</div>';
                $CODbtn .= '</div>';
            }
            return $CODbtn;
        })
        ->addColumn('action', function($row){

            $btn = '<a href="/get_customer_detail_for_edit/'.$row->id.'">';
                $btn .= '<button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm">แก้ไขข้อมูล</button>';
            $btn .= "</a>";

            return $btn;
        })
        ->rawColumns(['action' => 'action','cust_cod_register_status' => 'cust_cod_register_status'])
        ->make(true);
    }

    public function getCustomerDetailFormRenderBeforeEdit($id) {
        $customer = Customer::where('id', $id)->first();
        $provinces = province::get();
        $amphures = amphure::get();
        $Districts = District::get();
        if($customer) {
            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
           
            return view('Customers.customer_edit',compact(['customer','employee','provinces','amphures','Districts']));
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
            return redirect()->back();
        }
    }

    public function addNewSenderCustomer(Request $request) {
      
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
            return redirect()->to('/create_receive_jobs');
        }

        $customer = Customer::create([
            'cust_name' => $request->cust_name,
            'cust_address' => $request->cust_address,
            'cust_sub_district' => $request->cust_sub_district,
            'cust_district' => $request->cust_district,
            'cust_province' => $request->cust_province,
            'cust_postcode' => $request->cust_postcode,
            'cust_phone' => $request->cust_phone,
            'cust_status' => true
        ]);

        $search_phone = $customer->cust_phone;
        
        $customers = Customer::where('cust_phone',$search_phone)->get();
        alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $provinces = province::get();
        $sql = "SELECT a.id, a.name_th AS tname, a.zip_code, b.name_th AS aname, c.name_th AS pname  FROM districts a LEFT JOIN amphures b ON a.amphure_id = b.id LEFT JOIN provinces c ON b.province_id = c.id";
        $Districts = DB::select($sql);
       
        return view('Customers.customer_search',compact(["customers",'search_phone','employee','user','provinces','Districts']));
    }

    public function cod_account_detail(Request $request) {
        $CustomerCod = CustomerCod::where('id',$request->codid)->with('District')->with('amphure')->with('province')->get();
        // dd($CustomerCod);
        return json_encode($CustomerCod);
    }
    public function addCustomerCOD(Request $request) {
        if($request->codid == null){
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'cust_phone' => 'required',
                'cust_mail' => 'required',
                'cust_bookbank_name' => 'required',
                'cust_id_card' => 'required',
                'cust_bank_no' => 'required',
                'cust_bank_name' => 'required', 
                'cust_billing_address' => 'required', 
                'findAddressCOD' => 'required', 
                'cust_idcard_front_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg', 
                'cust_idcard_back_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg', 
                'cust_bookbank_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg', 
                'cust_sign_contract_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
            ]);
            if ($validator->fails()) {
                alert()->error('ขออภัย', 'กรุณาตรวจสอบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->to('/create_receive_jobs');
            }

            $customer = Customer::find($request->id);
            if($customer->cust_phone != $request->cust_phone){
                alert()->error('ขออภัย', 'หมายเลขมือถือไม่ตรงกับบัญชีลูกค้า')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->to('/create_receive_jobs');
            }
            $date = date('YmdHis');
            $image = $request->file('cust_idcard_front_img');
            $name = $date.'01.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploadimg/cod_account/'.$request->id.'/');
            $image->move($destinationPath, $name);
            $cust_idcard_front_img = $name;
            
            $image = $request->file('cust_idcard_back_img');
            $name = $date.'02.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploadimg/cod_account/'.$request->id.'/');
            $image->move($destinationPath, $name);
            $cust_idcard_back_img = $name;
            
            $image = $request->file('cust_bookbank_img');
            $name = $date.'03.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploadimg/cod_account/'.$request->id.'/');
            $image->move($destinationPath, $name);
            $cust_bookbank_img = $name;
            
            $image = $request->file('cust_sign_contract_img');
            $name = $date.'04.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploadimg/cod_account/'.$request->id.'/');
            $image->move($destinationPath, $name);
            $cust_sign_contract_img = $name;

            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $Districts = District::find($request->findAddressCOD);
            $CustomerCod = CustomerCod::create([
                'customer_id' => $request->id, 
                'cust_phone' => $customer->cust_phone, 
                'cust_mail' => $request->cust_mail,
                'cust_bookbank_name' => $request->cust_bookbank_name, 
                'cust_id_card' => $request->cust_id_card, 
                'cust_bank_no' => $request->cust_bank_no, 
                'cust_bank_name' => $request->cust_bank_name,
                'cust_billing_address' => $request->cust_billing_address, 
                'cust_sub_district' => $Districts->id,
                'cust_district' => $Districts->amphure->id,
                'cust_province' => $Districts->amphure->province->id,
                'cust_postcode' => $Districts->zip_code,
                'cust_idcard_front_img' => $cust_idcard_front_img,
                'cust_idcard_back_img' => $cust_idcard_back_img,
                'cust_bookbank_img' => $cust_bookbank_img,
                'cust_sign_contract_img' => $cust_sign_contract_img,
                'update_by' => $employee->id,
                'cod_status' => 1
            ]);

            $customer->update([
                'cust_cod_register_status' => $CustomerCod->id
            ]);

            $search_phone = $customer->cust_phone;
        }else{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'cust_phone' => 'required',
                'cust_mail' => 'required',
                'cust_bookbank_name' => 'required',
                'cust_id_card' => 'required',
                'cust_bank_no' => 'required',
                'cust_bank_name' => 'required', 
                'cust_billing_address' => 'required', 
                'findAddressCOD' => 'required'
            ]);
            if ($validator->fails()) {
                alert()->error('ขออภัย', 'กรุณาตรวจสอบข้อมูล')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->to('/create_receive_jobs');
            }

            $customer = Customer::find($request->id);
            if($customer->cust_phone != $request->cust_phone){
                alert()->error('ขออภัย', 'หมายเลขมือถือไม่ตรงกับบัญชีลูกค้า')->showConfirmButton('ตกลง', '#3085d6');
                return redirect()->to('/create_receive_jobs');
            }

            $user = Auth::user();
            $employee = Employee::where('id',$user->employee_id)->first();
            $Districts = District::find($request->findAddressCOD);
            $search_phone = $customer->cust_phone;

            $CustomerCod = CustomerCod::find($request->codid);
            $CustomerCod->update([
                'cust_phone' => $customer->cust_phone, 
                'cust_mail' => $request->cust_mail,
                'cust_bookbank_name' => $request->cust_bookbank_name, 
                'cust_id_card' => $request->cust_id_card, 
                'cust_bank_no' => $request->cust_bank_no, 
                'cust_bank_name' => $request->cust_bank_name,
                'cust_billing_address' => $request->cust_billing_address, 
                'cust_sub_district' => $Districts->id,
                'cust_district' => $Districts->amphure->id,
                'cust_province' => $Districts->amphure->province->id,
                'cust_postcode' => $Districts->zip_code,
                'update_by' => $employee->id,
                'cod_status' => 1
            ]);

            $date = date('YmdHis');
            if ($request->hasFile('cust_idcard_front_img')) {
                $image = $request->file('cust_idcard_front_img');
                $name = $date.'01.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('uploadimg/cod_account/'.$request->id.'/');
                $image->move($destinationPath, $name);
                $cust_idcard_front_img = $name;
                
                $CustomerCod->update([
                    'cust_idcard_front_img' => $cust_idcard_front_img
                ]);
            }
            
            if ($request->hasFile('cust_idcard_back_img')) {
                $image = $request->file('cust_idcard_back_img');
                $name = $date.'02.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('uploadimg/cod_account/'.$request->id.'/');
                $image->move($destinationPath, $name);
                $cust_idcard_back_img = $name;
                
                $CustomerCod->update([
                    'cust_idcard_back_img' => $cust_idcard_back_img
                ]);
            }
            
            if ($request->hasFile('cust_bookbank_img')) {
                $image = $request->file('cust_bookbank_img');
                $name = $date.'03.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('uploadimg/cod_account/'.$request->id.'/');
                $image->move($destinationPath, $name);
                $cust_bookbank_img = $name;
                
                $CustomerCod->update([
                    'cust_bookbank_img' => $cust_bookbank_img
                ]);
            }
            
            if ($request->hasFile('cust_sign_contract_img')) {
                $image = $request->file('cust_sign_contract_img');
                $name = $date.'04.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('uploadimg/cod_account/'.$request->id.'/');
                $image->move($destinationPath, $name);
                $cust_sign_contract_img = $name;

                $CustomerCod->update([
                    'cust_sign_contract_img' => $cust_sign_contract_img
                ]);
            }
        }
        

        // if ($request->hasFile('image')) {
            // $image = $request->file('cust_idcard_front_img');
            // $name = $image->getClientOriginalName(); //. '.' . $image->getClientOriginalExtension() นามสกุลไฟล์;
            // $destinationPath = public_path('uploadimg/cod_account/'.$request->id.'/');
            // File::delete(public_path('images/Environment/BusinessActivities/Products/'.$sRow->image.''));
            // $image->move($destinationPath, $name);
            // $cust_idcard_front_img = $name; // rowDB -> ชื่อ ิวDB  = ชื่อรูปที่สร้าง  
        // }
        if ($request->ManagementMenu == 'all') {
            return redirect()->back();
        } else {
            $customers = Customer::where('cust_phone',$search_phone)->get();
            $provinces = province::get();
            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
            return view('Customers.customer_search',compact(["customers",'search_phone','employee','user','provinces']));
        }
    }

    public function addNewReceiveCustomer(Request $request) {
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
            return redirect()->to('/create_receive_jobs');
        }

        $customer = Customer::create([
            'cust_name' => $request->cust_name,
            'cust_address' => $request->cust_address,
            'cust_sub_district' => $request->cust_sub_district,
            'cust_district' => $request->cust_district,
            'cust_province' => $request->cust_province,
            'cust_postcode' => $request->cust_postcode,
            'cust_phone' => $request->cust_phone,
            'cust_status' => true
        ]);

        $provinces = province::get(); 
        $search_phone = $customer->cust_phone;
        $tracking_id = $request->tracking_id;
        $customers = Customer::where('cust_phone',$search_phone)->get();
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง', '#3085d6');
        return view('Customers.customer_search_receive',compact(['customers','tracking_id','search_phone','employee','provinces']));
    }

    public function customer_management_add() {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $provinces = province::get();
       
        return view('Customers.customer_edit',compact('employee','provinces'));
    }
    
    public function find_areafromzipcode(Request $request) {
        $sql = "SELECT a.id, a.name_th AS tname, a.zip_code, b.name_th AS aname, c.name_th AS pname  FROM districts a LEFT JOIN amphures b ON a.amphure_id = b.id LEFT JOIN provinces c ON b.province_id = c.id where a.zip_code LIKE '$request->term%'";
        $Districts = DB::select($sql);
       
        return json_encode($Districts);
    }
}
