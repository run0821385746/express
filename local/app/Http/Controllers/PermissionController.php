<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\Permission;
use App\Model\AreaManagerBranch;
use Validator;
use Auth;
use DataTables;

class PermissionController extends Controller
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
            'sum_report_menu' => 'required',
            'receive_menu' => 'required',
            'parcel_care_menu' => 'required',
            'transfer_parcel_menu' => 'required',
            'request_service_menu' => 'required',
            'receive_parcel_from_dc_menu' => 'required',
            'parcel_status_wrong_menu' => 'required',
            'basic_information_menu' => 'required'
        ]);
  
        if ($validator->fails()) {
            alert()->error('ขออภัย','กรอกข้อมูลให้ครบ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }

        Permission::create([
            'emp_id' => '1',
            'sum_report_menu' => $request->sum_report_menu,
            'receive_menu' => $request->receive_menu,
            'parcel_care_menu' => $request->parcel_care_menu,
            'transfer_parcel_menu' => $request->transfer_parcel_menu,
            'request_service_menu' => $request->request_service_menu,
            'receive_parcel_from_dc_menu' => $request->receive_parcel_from_dc_menu,
            'parcel_status_wrong_menu' => $request->parcel_status_wrong_menu,
            'basic_information_menu' => $request->basic_information_menu,
            'permission_status' => true,
            'branch_id' => $user->emp_branch_id,
            'update_by' => $user->id
        ]);
        alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง','#3085d6');
        return redirect()->to('/permission_get_list/1');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function permissionGetList($branch_id = null) 
    {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == "เจ้าของกิจการ(Owner)"){
            $permissions = Permission::get();
            // dd($permissions);
        }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            $AreaManagerBranchs = AreaManagerBranch::where('employee_id', $employee->id)->get();
            if(count($AreaManagerBranchs) == 0){
                alert()->error('ขออภัย','คุณไม่มีพื้นที่สาขาในการดูแล')->showConfirmButton('ตกลง','#3085d6');
                return redirect()->back();
            }
            foreach ($AreaManagerBranchs as $key => $AreaManagerBranch) {
                if($key == 0){
                    $droup_center = "$AreaManagerBranch->branch_id";
                }else{
                    $droup_center .= ",$AreaManagerBranch->branch_id";
                }
            }
            $droup_center_array = explode(',', $droup_center);
            $permissions = Permission::whereIn('branch_id', $droup_center_array)->get();
            if(count($DropCenters) == 0){
                alert()->error('ขออภัย','คุณไม่มีพื้นที่สาขาในการดูแล')->showConfirmButton('ตกลง','#3085d6');
                return redirect()->back();
            }

        }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            $permissions = Permission::where('branch_id', $employee->emp_branch_id)->get();

        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }

        if ($permissions){
            return view('ManagementMenu.authen_management', compact(['permissions','employee']));
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->back();
        }  
    }

    public function permissionGetListDataTable(Request $request) {
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == "เจ้าของกิจการ(Owner)"){

            $permissions = Permission::get();
            
        }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){

            $AreaManagerBranchs = AreaManagerBranch::where('employee_id', $employee->id)->get();
            if(count($AreaManagerBranchs) == 0){
                alert()->error('ขออภัย','คุณไม่มีพื้นที่สาขาในการดูแล')->showConfirmButton('ตกลง','#3085d6');
                return redirect()->back();
            }
            foreach ($AreaManagerBranchs as $key => $AreaManagerBranch) {
                if($key == 0){
                    $droup_center = "$AreaManagerBranch->branch_id";
                }else{
                    $droup_center .= ",$AreaManagerBranch->branch_id";
                }
            }
            $droup_center_array = explode(',', $droup_center);
            $permissions = Permission::whereIn('branch_id', $droup_center_array)->get();

        }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){

            $permissions = Permission::where('branch_id', $employee->emp_branch_id)->get();

        }

        return Datatables::of($permissions)
        ->addIndexColumn()
        ->editColumn('emp_name',function($row){
            return $row->employee->emp_firstname.' '.$row->employee->emp_lastname;
        })
        ->editColumn('emp_position',function($row){
            return $row->employee->emp_position;
        })
        ->editColumn('daily_summaries_menu',function($row){
            if ($row->daily_summaries_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('parcel_care_menu',function($row){
            if ($row->parcel_care_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('receive_parcel_menu',function($row){
            if ($row->receive_parcel_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('all_parcel_menu',function($row){
            if ($row->all_parcel_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('parcel_cls_menu',function($row){
            if ($row->parcel_cls_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('parcel_send_menu',function($row){
            if ($row->parcel_send_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('parcel_call_recive_menu',function($row){
            if ($row->parcel_call_recive_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('recive_parcel_from_dc_menu',function($row){
            if ($row->recive_parcel_from_dc_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('orther_report_menu',function($row){
            if ($row->orther_report_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('customer_menu',function($row){
            if ($row->customer_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('employ_menu',function($row){
            if ($row->employ_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('permiss_menu',function($row){
            if ($row->permiss_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('dropcenter_menu',function($row){
            if ($row->dropcenter_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('orther_sale_menu',function($row){
            if ($row->orther_sale_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('service_price_menu',function($row){
            if ($row->service_price_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->editColumn('parcel_type_menu',function($row){
            if ($row->parcel_type_menu == 0){
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use check"></i>';
            }else{
                return '<i class="fa fa-fw" aria-hidden="true" title="Copy to use minus-circle"></i>';
            }
        })
        ->addColumn('action', function($row){
            $content = '';
            if($row->Employee->emp_position != "เจ้าของกิจการ(Owner)"){
                $content = '<a href="/permission/'.$row->id.'">';
                    $content .= '<button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm">ตั้งค่าใหม่</button>';
                $content .= '</a>';
            }
            return $content;
        })
        ->rawColumns([
            'daily_summaries_menu' => 'daily_summaries_menu',
            'parcel_care_menu' => 'parcel_care_menu',
            'receive_parcel_menu' => 'receive_parcel_menu',
            'all_parcel_menu' => 'all_parcel_menu',
            'parcel_cls_menu' => 'parcel_cls_menu',
            'parcel_send_menu' => 'parcel_send_menu',
            'parcel_call_recive_menu' => 'parcel_call_recive_menu',
            'recive_parcel_from_dc_menu' => 'recive_parcel_from_dc_menu',
            'orther_report_menu' => 'orther_report_menu',
            'customer_menu' => 'customer_menu',
            'employ_menu' => 'employ_menu',
            'permiss_menu' => 'permiss_menu',
            'dropcenter_menu' => 'dropcenter_menu',
            'orther_sale_menu' => 'orther_sale_menu',
            'service_price_menu' => 'service_price_menu',
            'parcel_type_menu' => 'parcel_type_menu',
            'action' => 'action'
            ])
        ->make(true);
    } 

    public function show($id)
    {
        $permissionDetail = Permission::where('id', $id)->first();
        $permissionfor = Employee::where('id', $permissionDetail->emp_id)->first();
        if($permissionDetail){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
            return view('ManagementMenu.authen_create', compact(['permissionDetail','permissionfor','employee']));
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
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
        $permissionDetail = Permission::find($id);
        if($permissionDetail){
            $permissionDetail->update([
                'daily_summaries_menu' => $request->daily_summaries_menu,
                'parcel_care_menu' => $request->parcel_care_menu,
                'receive_parcel_menu' => $request->receive_parcel_menu,
                'all_parcel_menu' => $request->all_parcel_menu,
                'parcel_cls_menu' => $request->parcel_cls_menu,
                'parcel_send_menu' => $request->parcel_send_menu,
                'parcel_call_recive_menu' => $request->parcel_call_recive_menu,
                'recive_parcel_from_dc_menu' => $request->recive_parcel_from_dc_menu,
                'orther_report_menu' => $request->orther_report_menu,
                'customer_menu' => $request->customer_menu,
                'employ_menu' => $request->employ_menu,
                'permiss_menu' => $request->permiss_menu,
                'dropcenter_menu' => $request->dropcenter_menu,
                'orther_sale_menu' => $request->orther_sale_menu,
                'service_price_menu' => $request->service_price_menu,
                'parcel_type_menu' => $request->parcel_type_menu,

            ]);
            alert()->success('สำเร็จ','บันทึกข้อมูลสำเร็จ')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->to('/permission_get_list/1');
        }else{
            alert()->error('ขออภัย','ไม่พบข้อมูล')->showConfirmButton('ตกลง','#3085d6');
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
