<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Model\User;
use App\Model\Employee;
use App\Model\Booking;
use App\Model\Permission;
use App\Model\Admin;
use App\Model\DropCenter;
use App\Model\Tracking;
use App\Model\Customer;
use App\Model\PacelCare;
use App\Model\Transfer;
use App\Model\TranserBill;
use App\Model\SubTracking;
use App\Model\TransferDropCenter;
use App\Model\TransferDropCenterBill;
use App\Model\TrackingsLog;
use App\Model\PostCode;
use App\Model\ParcelWrongs;
use App\Model\AreaManagerBranch;
use App\Model\CourierArea;
use App\Model\TranferDropCenterDuplicate;
use App\Model\ReciveTranferDropCenterDuplicate;
use App\Model\TransfersDuplicate;
use App\Model\CourierCall;
use App\Model\ReturnParcel;
use DB;
use Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getUser($id = null){
        $user = Auth::user();
        $userforup = User::where('employee_id',$user->employee_id)->first();
        $employee = Employee::where('id',$user->employee_id)->first();
        $DropCenters = DropCenter::get();
        if($id != null && $employee->emp_position == 'เจ้าของกิจการ(Owner)' || $employee->emp_position == 'ผู้จัดการเขตพื้นที่(Area Manager)'){
            $Permission = Permission::where('emp_id',$user->employee_id)->first();
            $Admin = Admin::where('employee_id',$user->employee_id)->first();
            
            $userforup->update([
                'emp_branch_id' => null
            ]);

            $employee->update([
                'emp_branch_id' => null
            ]);
            
            $Permission->update([
                'emp_branch_id' => null
            ]);
            
            $Admin->update([
                'emp_branch_id' => null
            ]);
        }
        return view('Dashboard.dashboard',compact('user','employee','DropCenters'));
        
        // if($id == null){
        //     if($user->employee->emp_branch_id == null){
        //         $employee = Employee::where('id',$user->employee_id)->first();
        //         return view('/welcome_owner',compact('employee'));
        //     }else{
        //         $employee = Employee::where('id',$user->employee_id)->first();
        //         return view('/welcome',compact('employee'));
        //     }
        // }else{
        //     $Employee = Employee::find($id);
        //     $Employee->update([
        //         'emp_branch_id' => null
        //     ]);
            
        //     $employee = Employee::where('id',$user->employee_id)->first();
        //     return view('/welcome_owner',compact('employee'));
        // }
    }

    public function getParcelCare($track_con = null){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        return view('ParcelCare.parcel_care',compact('employee','track_con'));
    }
    
    public function find_paarcel_care(Request $request){
        $strlen = strlen($request->tracking_no);
        if($strlen >= 15){
            $track_no = substr($request->tracking_no, 0, 15);
            $Tracking = Tracking::where('tracking_no', $track_no)->orwhere('tracking_no', 'like', $track_no.'%')->where('tracking_status', 'Destroy')->first();
            if(!empty($Tracking)){
                $created_at = substr($Tracking->created_at, 8,2).'/';
                $created_at .= substr($Tracking->created_at, 5,2).'/';
                $created_at .= substr($Tracking->created_at, 0,4).' ';
                $created_at .= substr($Tracking->created_at, 11,5);

                $SubTrackings = SubTracking::where('subtracking_tracking_id',$Tracking->id)->orderby('subtracking_under_tracking_id','ASC')->get();
                $parcel_amount = count($SubTrackings);
                $cod_amount = 0;
                $parcel_type = "";
                foreach ($SubTrackings as $key => $SubTracking) {
                    $cod_amount += $SubTracking->subtracking_cod;
                    if($key == 0){
                        $parcel_type .= '&nbsp;&nbsp;&nbsp;'.($key+1).'. '.$SubTracking->parceltype->parcel_type_name;
                    }else{
                        $parcel_type .= '<br>&nbsp;&nbsp;&nbsp;'.($key+1).'. '.$SubTracking->parceltype->parcel_type_name;
                    }
                }

                $Booking = Booking::find($Tracking->tracking_booking_id);
                $sender = Customer::find($Booking->booking_sender_id);
                $reciver = Customer::find($Tracking->tracking_receiver_id);

                $cancel_btn = "";
                if($Tracking->tracking_status == 'Destroy'){
                    $cancel_btn = "can_cancle_Destroy";
                }else if($Tracking->tracking_status == 'done'){
                    $Transfer = Transfer::where('transfer_tracking_id', $Tracking->id)->get();
                    if(count($Transfer) > 0){
                        $cancel_btn = "can_not_cancle_Destroy";
                    }else{
                        $cancel_btn = "can_Destroy";
                    }
                }else{
                    $cancel_btn = "can_not_cancle_Destroy";
                }

                $detail = '
                            {
                                "recive_date":"'.$created_at.'",
                                "sender":"'.trim(preg_replace('/\s\s+/', ' ', str_replace("	"," ","$sender->cust_name"))).' '.$sender->cust_phone.'<br> '.trim(preg_replace('/\s\s+/', ' ', str_replace("	"," ","$sender->cust_address"))).' '.$sender->District->name_th.'<br>'.$sender->amphure->name_th.' '.$sender->province->name_th.' '.$sender->cust_postcode.'",
                                "reciver":"'.trim(preg_replace('/\s\s+/', ' ', str_replace("	"," ","$reciver->cust_name"))).' '.$reciver->cust_phone.'<br>'.trim(preg_replace('/\s\s+/', ' ', str_replace("	"," ","$reciver->cust_address"))).' '.$reciver->District->name_th.'<br>'.$reciver->amphure->name_th.' '.$reciver->province->name_th.' '.$reciver->cust_postcode.'",
                                "parcel_amount":"'.$parcel_amount.'ชิ้น<hr style=\'margin-top:3px; margin-bottom:3px;\'><div align=\'left\'>ประเภทพัสดุ<br>'.$parcel_type.'</div>",
                                "track_cost":"'.$Tracking->tracking_amount.'",
                                "cod_amount":"'.$cod_amount.'",
                                "status":"'.$Tracking->tracking_status.'",
                                "tracking_id":"'.$Tracking->id.'",
                                "tracking_note":"'.$Tracking->tracking_note.'",
                                "destroy_tracking":"'.$cancel_btn.'"
                            }
                ';

                return $detail;
            }else{
                return '{"recive_date":"empty"}';
            }
        }else{
            return '{"recive_date":"empty"}';
        }
    }
    
    public function find_paarcel_care_moveing(Request $request){
        // dd($request->tracking_no);
        $html_return = '';
        $strlen = strlen($request->tracking_no);
        $track_no = substr($request->tracking_no, 0, 15);
        $show_2_rtn = 0;
        $inarray = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '23', '24');
        if(strpos($request->tracking_no, 'RTN') !== false){
            $show_2_rtn = 1;
            $inarray = array('12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22');
        }
        $Tracking = Tracking::where('tracking_no', $track_no)->orwhere('tracking_no', 'like', $track_no.'%')->where('tracking_status', 'Destroy')->first();
        $PacelCares = PacelCare::where('tracking_id', $Tracking->id)->whereIn('status', $inarray)->orderby('created_at', 'ASC')->get();
        // dd($PacelCares);
        $send_NO = 0;
        foreach ($PacelCares as $i => $PacelCare) {
            $datetime = date("d/m/Y H:i", strtotime($PacelCare->created_at));
            $html_return .= '<tr>';
                $html_return .= '<td>'.($i+1).'</td>';
                $html_return .= '<td class="text-left text-muted">'.$datetime.'น.</td>';
                if ($PacelCare->status == 9 || $PacelCare->status == 20) {
                    $Tracking = Tracking::find($PacelCare->tracking_id);
                    if($Tracking->tracking_status == 'CustomerResiveDone'){
                        $detail = 'ผู้รับปลายทางรับพัสดุ ';
                        $Transfer = Transfer::where('transfer_tracking_id', $PacelCare->tracking_id)->orderby('created_at', 'Desc')->first();
                        // dd($Transfer);
                        if($Transfer->transfer_bill_id == ''){
                            $detail .= 'ที่ DC';
                            $html_return .= '<td class="text-left"><span style="color:green; cursor:pointer;" onclick="status_9_detail(\'2\',\''.$PacelCare->Tracking->tracking_no.'\')">'.$PacelCare->PacelCareStatus->status.'('.$detail.')'.'</span></td>';
                        }else{
                            $detail .= 'ที่ หน้าบ้าน';
                            $html_return .= '<td class="text-left"><span style="color:green; cursor:pointer;" onclick="status_9_detail(\'1\',\''.$PacelCare->Tracking->tracking_no.'\')">'.$PacelCare->PacelCareStatus->status.'('.$detail.')'.'</span></td>';
                        }
                    }else if($Tracking->tracking_status == 'CustomerResiveDoneReturn'){
                        $detail = 'ผู้ส่งต้นทางรับคืนพัสดุ ที่ DC';
                        $html_return .= '<td class="text-left"><span style="color:blue; cursor:pointer;" onclick="status_9_detail(\'3\',\''.$PacelCare->Tracking->tracking_no.'\')">'.$PacelCare->PacelCareStatus->status.'('.$detail.')'.'</span></td>';
                    }
                } else if($PacelCare->status == 12){
                    $ParcelWrongs = ParcelWrongs::find($PacelCare->ref_no);
                    if($ParcelWrongs){
                        if($ParcelWrongs->wrong_status !== 'true'){
                            $employee = Employee::where('id',$ParcelWrongs->wrong_status)->first();
                            $html_return .= '<td class="text-left">'.$PacelCare->PacelCareStatus->status.'<span style="color:red;">(ยกเลิกโดย : '.$employee->emp_firstname.')</span></td>';
                        }else{
                            if($show_2_rtn == 1){
                                $html_return .= '<td class="text-left">'.$PacelCare->PacelCareStatus->status.'</td>';
                            }else{
                                $html_return .= '<td class="text-left">'.$PacelCare->PacelCareStatus->status.'<span style="color:red; cursor:pointer;" onclick="checkRTN(\'\')">(ตรวจสอบได้ที่รายการ RTN)</span></td>';
                            }
                        }
                    }else{
                        if($show_2_rtn == 1){
                            $html_return .= '<td class="text-left">'.$PacelCare->PacelCareStatus->status.'</td>';
                        }else{
                            $html_return .= '<td class="text-left">'.$PacelCare->PacelCareStatus->status.'<span style="color:red; cursor:pointer;" onclick="checkRTN(\'\')">(ตรวจสอบได้ที่รายการ RTN)</span></td>';
                        }
                    }
                } else {
                    if($PacelCare->status == 6 || $PacelCare->status == 17){
                        $send_NO++;
                    }
                    if($PacelCare->status == 8){
                        $CourierCalls = CourierCall::where('tracking_id', $PacelCare->tracking_id)->where('tranfer_id', $PacelCare->ref_no)->where('courier_id', $PacelCare->doing_by)->get();
                        // dd($CourierCalls);
                        $html_return .= '<td class="text-left">'.$PacelCare->PacelCareStatus->status.'<div class="badge badge-success" style="cursor:pointer;" onclick="view_c_call_table(\''.$i.'\')">View</div>';
                            $html_return .= '<table width="100%" id="c_call_table'.$i.'" style="display:none;">';
                            $html_return .= '<tr>';
                                $html_return .= '<th>เหตุผล</th>';
                                $html_return .= '<th>ระยะเวลาโทร</th>';
                                $html_return .= '<th>ระยะเวลาคุย</th>';
                                $html_return .= '<th>เวลาโทร</th>';
                            $html_return .= '</tr>';
                            foreach ($CourierCalls as $key => $CourierCall) {
                                $html_return .= '<tr>';
                                    $html_return .= '<td>'.$CourierCall->note.'</td>';
                                    $html_return .= '<td align=\'center\'>'.$CourierCall->oncall.'</td>';
                                    $html_return .= '<td align=\'center\'>'.$CourierCall->ontalk.'</td>';
                                    $html_return .= '<td>'.date("d/m/Y H:i", strtotime($CourierCall->callTime)).'</td>';
                                $html_return .= '</tr>';
                            }
                            $html_return .= '</table>';
                        $html_return .= '</td>';

                    }else if($PacelCare->status == 6 || $PacelCare->status == 7 || $PacelCare->status == 17 || $PacelCare->status == 18 || $PacelCare->status == 19){
                        if($send_NO > 1){
                            // $html_return .= '<td class="text-left">'.$PacelCare->PacelCareStatus->status.' <b>ครั้งที่'.$send_NO.'</b></td>';
                            $html_return .= '<td class="text-left">'.$PacelCare->PacelCareStatus->status.'</td>';
                        }else{
                            $html_return .= '<td class="text-left">'.$PacelCare->PacelCareStatus->status.'</td>';
                        }
                    }else{
                        $html_return .= '<td class="text-left">'.$PacelCare->PacelCareStatus->status.'</td>';
                    }
                }
                $html_return .= '<td class="text-left">'.$PacelCare->Employee->emp_firstname.' '.$PacelCare->Employee->emp_lastname.'</td>';
                $html_return .= '<td class="text-left">'.$PacelCare->DropCenter->drop_center_name_initial.'</td>';
                $html_return .= '<td class="text-center">';
                    $html_return .= '<div class="badge badge-success">Completed</div>';
                $html_return .= '</td>';
            $html_return .= '</tr>';
        }

        return json_encode($html_return);
    }

    public function getDashboard(){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $DropCenters = DropCenter::get();
        return view('Dashboard.dashboard',compact('employee','DropCenters'));
    }

    public function getdropcenter_for_owner(){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        if($employee->emp_position == 'เจ้าของกิจการ(Owner)'){
            $DropCenters = DropCenter::get();
            if(count($DropCenters) <= 0){
                alert()->error('ขออภัย','ไม่มีข้อมูลสาขา')->showConfirmButton('ตกลง','#3085d6');
                return redirect()->back();
            }
        }else if($employee->emp_position == 'ผู้จัดการเขตพื้นที่(Area Manager)'){
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
            $DropCenters = DropCenter::whereIn('id', $droup_center_array)->get();
            if(count($DropCenters) == 0){
                alert()->error('ขออภัย','คุณไม่มีพื้นที่สาขาในการดูแล')->showConfirmButton('ตกลง','#3085d6');
                return redirect()->back();
            }
        }
        return view('ManagementMenu.getdropcenter_for_owner',compact('employee','DropCenters'));
        // return json_encode($DropCenters);
    }
    
    public function select_drop_center($id = null){
        $user = Auth::user();
        $userforup = User::where('employee_id',$user->employee_id)->first();
        $employee = Employee::where('id',$user->employee_id)->first();
        $DropCenters = DropCenter::get();
        $Permission = Permission::where('emp_id',$user->employee_id)->first();
        $Admin = Admin::where('employee_id',$user->employee_id)->first();
        
        $userforup->update([
            'emp_branch_id' => $id
        ]);

        $employee->update([
            'emp_branch_id' => $id
        ]);
        
        $Permission->update([
            'emp_branch_id' => $id
        ]);
        
        $Admin->update([
            'emp_branch_id' => $id
        ]);


        return view('Dashboard.dashboard',compact('employee','DropCenters'));
        // return json_encode($DropCenters);
    }
    
    public function Destroy_tracking($id = null){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $Tracking = Tracking::find($id);

        if($Tracking->tracking_status == 'done'){
            $Transfer = Transfer::where('transfer_tracking_id', $Tracking->id)->get();
            if(count($Transfer) > 0){
                alert()->error('ไม่สำเร็จ','รายการนี้ได้มีการดำเนินการไปแล้ว')->showConfirmButton('ตกลง','#3085d6');
                return redirect()->to('/parcel_care/'.$Tracking->tracking_no);
            }else{
                $date = date('Y-m-d H:i:s');
                $tracking_no_destroy = $Tracking->tracking_no.'Destroy';
                $tracking_no = $Tracking->tracking_no;
                $Tracking->update([
                    'tracking_no' => $tracking_no_destroy,
                    'tracking_status' => 'Destroy'
                ]);

                $bookingData = Booking::find($Tracking->tracking_booking_id);
                $TrackingsLogs = TrackingsLog::create([
                    'tracking_no' => $tracking_no, 
                    'tracking_receiver_id' => $Tracking->tracking_receiver_id,
                    'tracking_status_id' => 15, 
                    'tracking_branch_id_dc' => $bookingData->booking_branch_id, 
                    'tracking_branch_id_sub_dc' => 0,
                    'tracking_date' => $date
                ]);
                
                $PacelCare = PacelCare::create([
                    'tracking_id' => $Tracking->id, 
                    'doing_by' => $employee->id,
                    'branch_id' => $bookingData->booking_branch_id, 
                    'status' => 23, 
                    'ref_no' => null
                ]);
            }
        }else{
            alert()->error('ไม่สำเร็จ','ไม่อยู่ในสถานะให้พร้อมยกเลิก')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->to('/parcel_care/'.$Tracking->tracking_no);
        }

        alert()->success('สำเร็จ','ยกเลิกรายการ Con สำเร็จ')->showConfirmButton('ตกลง','#3085d6');
        return redirect()->to('/parcel_care/'.$tracking_no);
    }
    
    public function cancle_Destroy_tracking($id = null){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $Tracking = Tracking::find($id);

        if($Tracking->tracking_status == 'Destroy'){

            $tracking_no = str_replace("Destroy","", $Tracking->tracking_no);
            $Tracking->update([
                'tracking_no' => $tracking_no,
                'tracking_status' => 'done'
            ]);

            $TrackingsLog = TrackingsLog::where('tracking_no', $tracking_no)->where('tracking_status_id', '15')->first();
            $TrackingsLog->delete();

            $bookingData = Booking::find($Tracking->tracking_booking_id);
            $PacelCare = PacelCare::create([
                'tracking_id' => $Tracking->id, 
                'doing_by' => $employee->id,
                'branch_id' => $bookingData->booking_branch_id, 
                'status' => 24, 
                'ref_no' => null
            ]);
        }else{
            alert()->error('ไม่สำเร็จ','ไม่อยู่ในสถานะให้กู้คืน')->showConfirmButton('ตกลง','#3085d6');
            return redirect()->to('/parcel_care/'.$tracking_no);
        }

        alert()->success('สำเร็จ','กู้คืนรายการ Con สำเร็จ')->showConfirmButton('ตกลง','#3085d6');
        return redirect()->to('/parcel_care/'.$tracking_no);
    }
}
