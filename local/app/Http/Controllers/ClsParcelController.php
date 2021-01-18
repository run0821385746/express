<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\Tracking;
use App\Model\SubTracking;
use App\Model\Customer;
use App\Model\DropCenter;
use App\Model\TransferDropCenter;
use Auth;
use DB;
use DataTables;

class ClsParcelController extends Controller
{
    public function getclsList() {
        $user = Auth::user();
        if($user){
            $employee = Employee::where('id',$user->employee_id)->first();
            $id = $employee->emp_branch_id;
            $amphure = DB::table('post_codes')
            ->select('amphures.name_th', 'amphures.id')
            ->leftJoin('districts', 'post_codes.postcode', '=', 'districts.zip_code')
            ->leftJoin('amphures', 'districts.amphure_id', '=', 'amphures.id')
            ->where('post_codes.drop_center_id', $id)
            ->groupby('amphures.id')
            ->get();
            
            $districs = DB::table('post_codes')
            ->select('districts.name_th', 'districts.id')
            ->leftJoin('districts', 'post_codes.postcode', '=', 'districts.zip_code')
            ->where('post_codes.drop_center_id', $id)
            ->groupby('districts.id')
            ->get();

            return view('Transfers.create_cls_parcel',compact(['employee','id','amphure','districs']));
        }else{
            alert()->error('ขออภัย', 'ไม่พบข้อมูลผู้ใช้งานระบบ')->showConfirmButton("ตกลง","#3085d6");
            return redirect()->back();
        }
    }

    public function cls_tracking_listFilter(Request $request){
        $branchid = $request->id;
        if ($request->ajax()) {
            if($request->ListType === "0"){
                // รายการทั้งหมด
                $sql = "
                SELECT
                    d.drop_center_id,
                    a.id as subtracking_cod,
                    b.booking_branch_id, d.drop_center_id,
                    b.booking_type, b.booking_no, b.booking_sender_id, c.cust_name,
                    a.*
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                WHERE
                    b.booking_branch_id = '$request->id' AND a.tracking_status = 'done' AND a.tracking_no != '' 
                    OR b.booking_branch_id = '$request->id' AND a.tracking_status = 'ReceiveDoneReturn'
                
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDone'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReturnBack'
                order by
                    a.created_at Desc
                ";
                //เอาออก 15/12/2020
                // OR b.booking_branch_id = '$request->id' AND a.tracking_status = 'transferDCDoing'
                // OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing'
                // OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier'
                // OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing'
                // OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier'
                
                
                // dd($sql);
                // $sql = "SELECT b.booking_branch_id, d.drop_center_id, b.booking_type, a.* FROM trackings a LEFT JOIN bookings b ON a.tracking_booking_id = b.id LEFT JOIN customers c ON a.tracking_receiver_id = c.id LEFT JOIN post_codes d ON c.cust_postcode = d.postcode WHERE  b.booking_branch_id = '$request->id' OR d.drop_center_id = '$request->id'";
                $trackingList = DB::select($sql);

                // $sql = "SELECT n.transfer_recriver_id, m.transfer_dropcenter_status, e.tracking_status, f.booking_no, f.booking_type, g.cust_name, sum(h.subtracking_cod) as subtracking_cod, e.* FROM trackings e LEFT JOIN bookings f ON e.tracking_booking_id = f.id LEFT JOIN customers g ON e.tracking_receiver_id = g.id LEFT JOIN sub_trackings h ON h.subtracking_tracking_id = e.id LEFT JOIN transfer_drop_centers m ON m.transfer_dropcenter_tracking_no = e.tracking_no LEFT JOIN transfer_drop_center_bills n ON n.id = m.transfer_bill_id_ref  WHERE e.tracking_no IN (SELECT a.tracking_no FROM trackings a WHERE a.tracking_booking_id IN (SELECT b.id FROM bookings b WHERE b.booking_branch_id = '$request->id')) OR e.tracking_no IN (SELECT c.transfer_dropcenter_tracking_no FROM transfer_drop_centers c WHERE c.transfer_bill_id_ref IN (SELECT d.id FROM transfer_drop_center_bills d WHERE d.transfer_recriver_id = '$request->id')) GROUP BY e.id";
                // $trackingList = DB::select($sql);

                return Datatables::of($trackingList)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row){
                    $DropCenter = DropCenter::find($row->booking_branch_id);
                    $SubTrackings = SubTracking::where('subtracking_tracking_id',$row->id)->get();
                    $parcelamount = count($SubTrackings);

                    $created_at = substr($row->created_at, 8,2).'/';
                    $created_at .= substr($row->created_at, 5,2).'/';
                    $created_at .= substr($row->created_at, 0,4).' ';
                    $created_at .= substr($row->created_at, 11,5);

                    $orther_dc_revice_time = substr($row->orther_dc_revice_time, 8,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 5,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 0,4).' ';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 11,5);
                    
                    if (strpos($row->tracking_status, 'Return') !== false) {
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'(RTN)</a>';
                    }else{
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'</a>';
                    }
                    // $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'</a>';
                    return $tracking_no;
                })
                ->editColumn('cust_send_name',function($row){
                    $Customer = Customer::find($row->booking_sender_id);
                    return $Customer->cust_name;
                })
                ->editColumn('booking_type',function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->booking_type == '1'){
                            return $booking_type = 'พัสดุรับหน้าร้าน';
                        }else{
                            return $booking_type = 'เรียกรถเข้ารับพัสดุ';
                        }
                    }else{
                        return $booking_type = 'พัสดุรับจากสาขาต้นทาง';
                    }
                })
                ->editColumn('subtracking_cod',function($row){
                    $cod = 0;
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->id)->get();
                    foreach ($SubTrackings as $SubTracking) {
                        $cod += $SubTracking->subtracking_cod;
                    }
                    return number_format($cod,2);
                })
                ->editColumn('tracking_status', function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->tracking_status == 'done'){
                            if($branchid == $row->drop_center_id){
                                return $tracking_status = 'รอนำส่งลูกค้า';
                            }else{
                                return $tracking_status = 'รอส่งให้ปลายทาง';
                            }
                        }else if($row->tracking_status == 'transferDCDoing'){

                            return $tracking_status = 'ทำส่งปลายทาง';

                        }else if($row->tracking_status == 'transferDoing'){

                            return $tracking_status = 'ทำเบิก(COURIER)';

                        }else if($row->tracking_status == 'TransferToCourier'){

                            return $tracking_status = 'กำลังนำส่ง(COURIER)';

                        }else if($row->tracking_status == 'ReceiveDoneReturn'){

                            return $tracking_status = '<span style="color:red;">รายการรับคืน</span>';

                        }else{

                            return $tracking_status = 'ไม่ทราบสถานะ';

                        }
                    }else{

                        if($row->tracking_status == 'ReceiveDone'){

                            return $tracking_status = 'รอนำส่งลูกค้า';

                        }else if($row->tracking_status == 'transferDoing'){

                            return $tracking_status = 'ทำเบิก(COURIER)';

                        }else if($row->tracking_status == 'TransferToCourier'){

                            return $tracking_status = 'กำลังนำส่ง(COURIER)';

                        }else if($row->tracking_status == 'ReturnBack'){

                            return $tracking_status = '<span style="color:red;">รอส่งกลับต้นทาง</span>';

                        }else{

                            return $tracking_status = 'ไม่ทราบสถานะ';

                        }
                    }
                })
                ->editColumn('orther_dc_revice_time',function($row){
                    if($row->orther_dc_revice_time == null){
                        $date = substr($row->created_at, 8,2).'/';
                        $date .= substr($row->created_at, 5,2).'/';
                        $date .= substr($row->created_at, 0,4).' ';
                        $date .= substr($row->created_at, 11,5);
                    }else{
                        $date = substr($row->orther_dc_revice_time, 8,2).'/';
                        $date .= substr($row->orther_dc_revice_time, 5,2).'/';
                        $date .= substr($row->orther_dc_revice_time, 0,4).' ';
                        $date .= substr($row->orther_dc_revice_time, 11,5);
                    }
                    return $date;
                })
                ->addColumn('action', function($row) {
                    if($row->tracking_status == 'ReturnBack'){
                        
                        return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->id.'\');">ยกเลิกส่งกลับ</button>';

                    }else if($row->tracking_status == 'ReceiveDoneReturn'){

                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->id)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        }else{
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->id.'\');">ยกเลิกส่งกลับ</button>';
                        }

                    }else{
                        if($row->tracking_status == 'done' || $row->tracking_status == 'ReceiveDone'){
                            
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" onClick="addStatusWrong(\''.$row->id.'\');">แจ้งส่งกลับ</button>';
                        
                        }else{
                            
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        
                        }
                    
                    }
                })
                ->rawColumns(['action' => 'action','tracking_no' => 'tracking_no','tracking_status' => 'tracking_status'])
                ->make(true);
            }else if($request->ListType === "1"){
                // $sql = "
                // SELECT
                //     d.drop_center_id,
                //     a.id as subtracking_cod,
                //     b.booking_branch_id, d.drop_center_id,
                //     b.booking_type, b.booking_no, b.booking_sender_id, c.cust_name,
                //     a.*
                // FROM
                //     trackings a
                //     LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                //     LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                //     LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                // WHERE
                //     b.booking_branch_id = '$request->id' AND a.tracking_status = 'done' AND c.cust_district = '$request->ap'
                //     OR b.booking_branch_id = '$request->id' AND a.tracking_status = 'transferDCDoing' AND c.cust_district = '$request->ap'
                //     OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND c.cust_district = '$request->ap'
                //     OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND c.cust_district = '$request->ap'
                
                //     OR d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDone' AND c.cust_district = '$request->ap'
                //     OR d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND c.cust_district = '$request->ap'
                //     OR d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND c.cust_district = '$request->ap'
                //     OR d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND c.cust_district = '$request->ap'
                // order by
                //     a.created_at Desc
                // ";
                $sql = "
                SELECT
                    d.drop_center_id,
                    a.id as subtracking_cod,
                    b.booking_branch_id, d.drop_center_id,
                    b.booking_type, b.booking_no, b.booking_sender_id, c.cust_name,
                    a.*
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                WHERE
                    b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'done' AND c.cust_district = '$request->ap'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDoneReturn' AND c.cust_district = '$request->ap'
                    
                
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDone' AND c.cust_district = '$request->ap'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReturnBack' AND c.cust_district = '$request->ap'
                order by
                    a.created_at Desc
                ";
                // เอาออก 15/12/2020
                // OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND c.cust_district = '$request->ap'
                // OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND c.cust_district = '$request->ap'
                // OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND c.cust_district = '$request->ap'
                // OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND c.cust_district = '$request->ap'

                // dd($sql);
                // $sql = "SELECT b.booking_branch_id, d.drop_center_id, b.booking_type, a.* FROM trackings a LEFT JOIN bookings b ON a.tracking_booking_id = b.id LEFT JOIN customers c ON a.tracking_receiver_id = c.id LEFT JOIN post_codes d ON c.cust_postcode = d.postcode WHERE  b.booking_branch_id = '$request->id' OR d.drop_center_id = '$request->id'";
                $trackingList = DB::select($sql);

                // $sql = "SELECT n.transfer_recriver_id, m.transfer_dropcenter_status, e.tracking_status, f.booking_no, f.booking_type, g.cust_name, sum(h.subtracking_cod) as subtracking_cod, e.* FROM trackings e LEFT JOIN bookings f ON e.tracking_booking_id = f.id LEFT JOIN customers g ON e.tracking_receiver_id = g.id LEFT JOIN sub_trackings h ON h.subtracking_tracking_id = e.id LEFT JOIN transfer_drop_centers m ON m.transfer_dropcenter_tracking_no = e.tracking_no LEFT JOIN transfer_drop_center_bills n ON n.id = m.transfer_bill_id_ref  WHERE e.tracking_no IN (SELECT a.tracking_no FROM trackings a WHERE a.tracking_booking_id IN (SELECT b.id FROM bookings b WHERE b.booking_branch_id = '$request->id')) OR e.tracking_no IN (SELECT c.transfer_dropcenter_tracking_no FROM transfer_drop_centers c WHERE c.transfer_bill_id_ref IN (SELECT d.id FROM transfer_drop_center_bills d WHERE d.transfer_recriver_id = '$request->id')) GROUP BY e.id";
                // $trackingList = DB::select($sql);

                return Datatables::of($trackingList)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row){
                    $DropCenter = DropCenter::find($row->booking_branch_id);
                    $SubTrackings = SubTracking::where('subtracking_tracking_id',$row->id)->get();
                    $parcelamount = count($SubTrackings);

                    $created_at = substr($row->created_at, 8,2).'/';
                    $created_at .= substr($row->created_at, 5,2).'/';
                    $created_at .= substr($row->created_at, 0,4).' ';
                    $created_at .= substr($row->created_at, 11,5);

                    $orther_dc_revice_time = substr($row->orther_dc_revice_time, 8,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 5,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 0,4).' ';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 11,5);
                    
                    if (strpos($row->tracking_status, 'Return') !== false) {
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'(RTN)</a>';
                    }else{
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'</a>';
                    }
                    return $tracking_no;
                })
                ->editColumn('cust_send_name',function($row){
                    $Customer = Customer::find($row->booking_sender_id);
                    return $Customer->cust_name;
                })
                ->editColumn('booking_type',function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->booking_type == '1'){
                            return $booking_type = 'พัสดุรับหน้าร้าน';
                        }else{
                            return $booking_type = 'เรียกรถเข้ารับพัสดุ';
                        }
                    }else{
                        return $booking_type = 'พัสดุรับจากสาขาต้นทาง';
                    }
                })
                ->editColumn('subtracking_cod',function($row){
                    $cod = 0;
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->id)->get();
                    foreach ($SubTrackings as $SubTracking) {
                        $cod += $SubTracking->subtracking_cod;
                    }
                    return number_format($cod,2);
                })
                ->editColumn('tracking_status', function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->tracking_status == 'done'){
                            if($branchid == $row->drop_center_id){
                                return $tracking_status = 'รอนำส่งลูกค้า';
                            }else{
                                return $tracking_status = 'รอส่งให้ปลายทาง';
                            }
                        }else if($row->tracking_status == 'transferDCDoing'){

                            return $tracking_status = 'ทำส่งปลายทาง';

                        }else if($row->tracking_status == 'transferDoing'){

                            return $tracking_status = 'ทำเบิก(COURIER)';

                        }else if($row->tracking_status == 'TransferToCourier'){

                            return $tracking_status = 'กำลังนำส่ง(COURIER)';

                        }else if($row->tracking_status == 'ReceiveDoneReturn'){

                            return $tracking_status = '<span style="color:red;">รายการรับคืน</span>';

                        }else{

                            return $tracking_status = 'ไม่ทราบสถานะ';

                        }
                    }else{

                        if($row->tracking_status == 'ReceiveDone'){

                            return $tracking_status = 'รอนำส่งลูกค้า';

                        }else if($row->tracking_status == 'transferDoing'){

                            return $tracking_status = 'ทำเบิก(COURIER)';

                        }else if($row->tracking_status == 'TransferToCourier'){

                            return $tracking_status = 'กำลังนำส่ง(COURIER)';

                        }else if($row->tracking_status == 'ReturnBack'){

                            return $tracking_status = '<span style="color:red;">รอส่งกลับต้นทาง</span>';

                        }else{

                            return $tracking_status = 'ไม่ทราบสถานะ';

                        }
                    }
                })
                ->editColumn('orther_dc_revice_time',function($row){
                    if($row->orther_dc_revice_time == null){
                        $date = substr($row->created_at, 8,2).'/';
                        $date .= substr($row->created_at, 5,2).'/';
                        $date .= substr($row->created_at, 0,4).' ';
                        $date .= substr($row->created_at, 11,5);
                    }else{
                        $date = substr($row->orther_dc_revice_time, 8,2).'/';
                        $date .= substr($row->orther_dc_revice_time, 5,2).'/';
                        $date .= substr($row->orther_dc_revice_time, 0,4).' ';
                        $date .= substr($row->orther_dc_revice_time, 11,5);
                    }
                    return $date;
                })
                ->addColumn('action', function($row) {
                    if($row->tracking_status == 'ReturnBack'){
                        
                        return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->id.'\');">ยกเลิกส่งกลับ</button>';

                    }else if($row->tracking_status == 'ReceiveDoneReturn'){

                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->id)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        }else{
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->id.'\');">ยกเลิกส่งกลับ</button>';
                        }

                    }else{
                        if($row->tracking_status == 'done' || $row->tracking_status == 'ReceiveDone'){
                            
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" onClick="addStatusWrong(\''.$row->id.'\');">แจ้งส่งกลับ</button>';
                        
                        }else{
                            
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        
                        }
                    
                    }
                })
                ->rawColumns(['action' => 'action','tracking_no' => 'tracking_no','tracking_status' => 'tracking_status'])
                ->make(true);
            }else if($request->ListType === "2"){
                // $sql = "
                // SELECT
                //     d.drop_center_id,
                //     a.id as subtracking_cod,
                //     b.booking_branch_id, d.drop_center_id,
                //     b.booking_type, b.booking_no, b.booking_sender_id, c.cust_name,
                //     a.*
                // FROM
                //     trackings a
                //     LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                //     LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                //     LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                // WHERE
                //     b.booking_branch_id = '$request->id' AND a.tracking_status = 'done' AND c.cust_sub_district = '$request->dt'
                //     OR b.booking_branch_id = '$request->id' AND a.tracking_status = 'transferDCDoing' AND c.cust_sub_district = '$request->dt'
                //     OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND c.cust_sub_district = '$request->dt'
                //     OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND c.cust_sub_district = '$request->dt'
                
                //     OR d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDone' AND c.cust_sub_district = '$request->dt'
                //     OR d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND c.cust_sub_district = '$request->dt'
                //     OR d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND c.cust_sub_district = '$request->dt'
                // order by
                //     a.created_at Desc
                // ";
                $sql = "
                SELECT
                    d.drop_center_id,
                    a.id as subtracking_cod,
                    b.booking_branch_id, d.drop_center_id,
                    b.booking_type, b.booking_no, b.booking_sender_id, c.cust_name,
                    a.*
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                WHERE
                    b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'done' AND c.cust_sub_district = '$request->dt'
                    OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDoneReturn' AND c.cust_sub_district = '$request->dt'
                    
                
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReceiveDone' AND c.cust_sub_district = '$request->dt'
                    OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'ReturnBack' AND c.cust_sub_district = '$request->dt'
                order by
                    a.created_at Desc
                ";
                // เอาออก 15/12/2020
                // OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND c.cust_sub_district = '$request->dt'
                // OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND c.cust_sub_district = '$request->dt'
                // OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'transferDoing' AND c.cust_sub_district = '$request->dt'
                // OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'TransferToCourier' AND c.cust_sub_district = '$request->dt'

                // dd($sql);
                // $sql = "SELECT b.booking_branch_id, d.drop_center_id, b.booking_type, a.* FROM trackings a LEFT JOIN bookings b ON a.tracking_booking_id = b.id LEFT JOIN customers c ON a.tracking_receiver_id = c.id LEFT JOIN post_codes d ON c.cust_postcode = d.postcode WHERE  b.booking_branch_id = '$request->id' OR d.drop_center_id = '$request->id'";
                $trackingList = DB::select($sql);

                // $sql = "SELECT n.transfer_recriver_id, m.transfer_dropcenter_status, e.tracking_status, f.booking_no, f.booking_type, g.cust_name, sum(h.subtracking_cod) as subtracking_cod, e.* FROM trackings e LEFT JOIN bookings f ON e.tracking_booking_id = f.id LEFT JOIN customers g ON e.tracking_receiver_id = g.id LEFT JOIN sub_trackings h ON h.subtracking_tracking_id = e.id LEFT JOIN transfer_drop_centers m ON m.transfer_dropcenter_tracking_no = e.tracking_no LEFT JOIN transfer_drop_center_bills n ON n.id = m.transfer_bill_id_ref  WHERE e.tracking_no IN (SELECT a.tracking_no FROM trackings a WHERE a.tracking_booking_id IN (SELECT b.id FROM bookings b WHERE b.booking_branch_id = '$request->id')) OR e.tracking_no IN (SELECT c.transfer_dropcenter_tracking_no FROM transfer_drop_centers c WHERE c.transfer_bill_id_ref IN (SELECT d.id FROM transfer_drop_center_bills d WHERE d.transfer_recriver_id = '$request->id')) GROUP BY e.id";
                // $trackingList = DB::select($sql);

                return Datatables::of($trackingList)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row){
                    $DropCenter = DropCenter::find($row->booking_branch_id);
                    $SubTrackings = SubTracking::where('subtracking_tracking_id',$row->id)->get();
                    $parcelamount = count($SubTrackings);

                    $created_at = substr($row->created_at, 8,2).'/';
                    $created_at .= substr($row->created_at, 5,2).'/';
                    $created_at .= substr($row->created_at, 0,4).' ';
                    $created_at .= substr($row->created_at, 11,5);

                    $orther_dc_revice_time = substr($row->orther_dc_revice_time, 8,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 5,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 0,4).' ';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 11,5);
                    
                    if (strpos($row->tracking_status, 'Return') !== false) {
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'(RTN)</a>';
                    }else{
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'</a>';
                    }
                    return $tracking_no;
                })
                ->editColumn('cust_send_name',function($row){
                    $Customer = Customer::find($row->booking_sender_id);
                    return $Customer->cust_name;
                })
                ->editColumn('booking_type',function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->booking_type == '1'){
                            return $booking_type = 'พัสดุรับหน้าร้าน';
                        }else{
                            return $booking_type = 'เรียกรถเข้ารับพัสดุ';
                        }
                    }else{
                        return $booking_type = 'พัสดุรับจากสาขาต้นทาง';
                    }
                })
                ->editColumn('subtracking_cod',function($row){
                    $cod = 0;
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->id)->get();
                    foreach ($SubTrackings as $SubTracking) {
                        $cod += $SubTracking->subtracking_cod;
                    }
                    return number_format($cod,2);
                })
                ->editColumn('tracking_status', function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->tracking_status == 'done'){
                            if($branchid == $row->drop_center_id){
                                return $tracking_status = 'รอนำส่งลูกค้า';
                            }else{
                                return $tracking_status = 'รอส่งให้ปลายทาง';
                            }
                        }else if($row->tracking_status == 'transferDCDoing'){

                            return $tracking_status = 'ทำส่งปลายทาง';

                        }else if($row->tracking_status == 'transferDoing'){

                            return $tracking_status = 'ทำเบิก(COURIER)';

                        }else if($row->tracking_status == 'TransferToCourier'){

                            return $tracking_status = 'กำลังนำส่ง(COURIER)';

                        }else if($row->tracking_status == 'ReceiveDoneReturn'){

                            return $tracking_status = '<span style="color:red;">รายการรับคืน</span>';

                        }else{

                            return $tracking_status = 'ไม่ทราบสถานะ';

                        }
                    }else{

                        if($row->tracking_status == 'ReceiveDone'){

                            return $tracking_status = 'รอนำส่งลูกค้า';

                        }else if($row->tracking_status == 'transferDoing'){

                            return $tracking_status = 'ทำเบิก(COURIER)';

                        }else if($row->tracking_status == 'TransferToCourier'){

                            return $tracking_status = 'กำลังนำส่ง(COURIER)';

                        }else if($row->tracking_status == 'ReturnBack'){

                            return $tracking_status = '<span style="color:red;">รอส่งกลับต้นทาง</span>';

                        }else{

                            return $tracking_status = 'ไม่ทราบสถานะ';

                        }
                    }
                })
                ->editColumn('orther_dc_revice_time',function($row){
                    if($row->orther_dc_revice_time == null){
                        $date = substr($row->created_at, 8,2).'/';
                        $date .= substr($row->created_at, 5,2).'/';
                        $date .= substr($row->created_at, 0,4).' ';
                        $date .= substr($row->created_at, 11,5);
                    }else{
                        $date = substr($row->orther_dc_revice_time, 8,2).'/';
                        $date .= substr($row->orther_dc_revice_time, 5,2).'/';
                        $date .= substr($row->orther_dc_revice_time, 0,4).' ';
                        $date .= substr($row->orther_dc_revice_time, 11,5);
                    }
                    return $date;
                })
                ->addColumn('action', function($row) {
                    if($row->tracking_status == 'ReturnBack'){
                        
                        return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->id.'\');">ยกเลิกส่งกลับ</button>';

                    }else if($row->tracking_status == 'ReceiveDoneReturn'){

                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->id)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        }else{
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->id.'\');">ยกเลิกส่งกลับ</button>';
                        }

                    }else{
                        if($row->tracking_status == 'done' || $row->tracking_status == 'ReceiveDone'){
                            
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" onClick="addStatusWrong(\''.$row->id.'\');">แจ้งส่งกลับ</button>';
                        
                        }else{
                            
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        
                        }
                    
                    }
                })
                ->rawColumns(['action' => 'action','tracking_no' => 'tracking_no','tracking_status' => 'tracking_status'])
                ->make(true);

            }else if($request->ListType === "3"){
                $sql = "
                    SELECT
                        a.id as subtracking_cod,
                        b.booking_branch_id, d.drop_center_id,
                        b.booking_type, b.booking_no, b.booking_sender_id, c.cust_name,
                        a.*
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                    WHERE
                        b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'CustomerResiveDone'
                        OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'CustomerResiveDone'
                        OR b.booking_branch_id = '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'CustomerResiveDoneReturn'
                        OR b.booking_branch_id != '$request->id' AND d.drop_center_id = '$request->id' AND a.tracking_status = 'CustomerResiveDoneReturn'
                    order by
                        a.created_at Desc
                ";

                $trackingList = DB::select($sql);

                return Datatables::of($trackingList)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row){
                    $DropCenter = DropCenter::find($row->booking_branch_id);
                    $SubTrackings = SubTracking::where('subtracking_tracking_id',$row->id)->get();
                    $parcelamount = count($SubTrackings);

                    $created_at = substr($row->created_at, 8,2).'/';
                    $created_at .= substr($row->created_at, 5,2).'/';
                    $created_at .= substr($row->created_at, 0,4).' ';
                    $created_at .= substr($row->created_at, 11,5);

                    $orther_dc_revice_time = substr($row->orther_dc_revice_time, 8,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 5,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 0,4).' ';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 11,5);
                    
                    if (strpos($row->tracking_status, 'Return') !== false) {
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'(RTN)</a>';
                    }else{
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'</a>';
                    }
                    return $tracking_no;
                })
                ->editColumn('cust_send_name',function($row){
                    $Customer = Customer::find($row->booking_sender_id);
                    return $Customer->cust_name;
                })
                ->editColumn('booking_type',function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->booking_type == '1'){
                            return $booking_type = 'พัสดุรับหน้าร้าน';
                        }else{
                            return $booking_type = 'เรียกรถเข้ารับพัสดุ';
                        }
                    }else{
                        return $booking_type = 'พัสดุรับจากสาขาต้นทาง';
                    }
                })
                ->editColumn('subtracking_cod',function($row){
                    $cod = 0;
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->id)->get();
                    foreach ($SubTrackings as $SubTracking) {
                        $cod += $SubTracking->subtracking_cod;
                    }
                    return number_format($cod,2);
                })
                ->editColumn('tracking_status', function($row) use($branchid){
                    return $tracking_status = '<p style="color:green">ลูกค้าปลายทางรับพัสดุแล้ว</p>';
                })
                ->editColumn('orther_dc_revice_time',function($row){
                    if($row->orther_dc_revice_time == null){
                        $date = substr($row->created_at, 8,2).'/';
                        $date .= substr($row->created_at, 5,2).'/';
                        $date .= substr($row->created_at, 0,4).' ';
                        $date .= substr($row->created_at, 11,5);
                    }else{
                        $date = substr($row->orther_dc_revice_time, 8,2).'/';
                        $date .= substr($row->orther_dc_revice_time, 5,2).'/';
                        $date .= substr($row->orther_dc_revice_time, 0,4).' ';
                        $date .= substr($row->orther_dc_revice_time, 11,5);
                    }
                    return $date;
                })
                ->addColumn('action', function($row) {
                    return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>';
                })
                ->rawColumns(['action' => 'action','tracking_no' => 'tracking_no','tracking_status' => 'tracking_status'])
                ->make(true);
            }else if($request->ListType === "4"){
                $sql = "
                    SELECT
                        a.id as subtracking_cod,
                        b.booking_branch_id, d.drop_center_id,
                        b.booking_type, b.booking_no, b.booking_sender_id, c.cust_name,
                        a.*
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                    WHERE
                        b.booking_branch_id = '$request->id' AND d.drop_center_id != '$request->id' AND a.tracking_status = 'done'
                    order by
                        a.created_at Desc
                ";
                $trackingList = DB::select($sql);

                return Datatables::of($trackingList)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row){
                    $DropCenter = DropCenter::find($row->booking_branch_id);
                    $SubTrackings = SubTracking::where('subtracking_tracking_id',$row->id)->get();
                    $parcelamount = count($SubTrackings);

                    $created_at = substr($row->created_at, 8,2).'/';
                    $created_at .= substr($row->created_at, 5,2).'/';
                    $created_at .= substr($row->created_at, 0,4).' ';
                    $created_at .= substr($row->created_at, 11,5);

                    $orther_dc_revice_time = substr($row->orther_dc_revice_time, 8,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 5,2).'/';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 0,4).' ';
                    $orther_dc_revice_time .= substr($row->orther_dc_revice_time, 11,5);
                    
                    if (strpos($row->tracking_status, 'Return') !== false) {
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'(RTN)</a>';
                    }else{
                        $tracking_no = '<a href="#" onclick="viewDetail(\''.$DropCenter->drop_center_name_initial.'\',\''.$row->booking_no.'\',\''.$row->tracking_no.'\',\''.$parcelamount.'\',\''.$created_at.'\',\''.$orther_dc_revice_time.'\',\''.$row->booking_sender_id.'\',\''.$row->tracking_receiver_id.'\')">'.$row->tracking_no.'</a>';
                    }
                    return $tracking_no;
                })
                ->editColumn('cust_send_name',function($row){
                    $Customer = Customer::find($row->booking_sender_id);
                    return $Customer->cust_name;
                })
                ->editColumn('booking_type',function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->booking_type == '1'){
                            return $booking_type = 'พัสดุรับหน้าร้าน';
                        }else{
                            return $booking_type = 'เรียกรถเข้ารับพัสดุ';
                        }
                    }else{
                        return $booking_type = 'พัสดุรับจากสาขาต้นทาง';
                    }
                })
                ->editColumn('subtracking_cod',function($row){
                    $cod = 0;
                    $SubTrackings = SubTracking::where('subtracking_tracking_id', $row->id)->get();
                    foreach ($SubTrackings as $SubTracking) {
                        $cod += $SubTracking->subtracking_cod;
                    }
                    return number_format($cod,2);
                })
                ->editColumn('tracking_status', function($row) use($branchid){
                    if($branchid == $row->booking_branch_id){
                        if($row->tracking_status == 'done'){
                            if($branchid == $row->drop_center_id){
                                return $tracking_status = 'รอนำส่งลูกค้า';
                            }else{
                                return $tracking_status = 'รอส่งให้ปลายทาง';
                            }
                        }else if($row->tracking_status == 'transferDCDoing'){

                            return $tracking_status = 'ทำส่งปลายทาง';

                        }else if($row->tracking_status == 'transferDoing'){

                            return $tracking_status = 'ทำเบิก(COURIER)';

                        }else if($row->tracking_status == 'TransferToCourier'){

                            return $tracking_status = 'กำลังนำส่ง(COURIER)';

                        }else if($row->tracking_status == 'ReceiveDoneReturn'){

                            return $tracking_status = '<span style="color:red;">รายการรับคืน</span>';

                        }else{

                            return $tracking_status = 'ไม่ทราบสถานะ';

                        }
                    }else{

                        if($row->tracking_status == 'ReceiveDone'){

                            return $tracking_status = 'รอนำส่งลูกค้า';

                        }else if($row->tracking_status == 'transferDoing'){

                            return $tracking_status = 'ทำเบิก(COURIER)';

                        }else if($row->tracking_status == 'TransferToCourier'){

                            return $tracking_status = 'กำลังนำส่ง(COURIER)';

                        }else if($row->tracking_status == 'ReturnBack'){

                            return $tracking_status = '<span style="color:red;">รอส่งกลับต้นทาง</span>';

                        }else{

                            return $tracking_status = 'ไม่ทราบสถานะ';

                        }
                    }
                })
                ->editColumn('orther_dc_revice_time',function($row){
                    if($row->orther_dc_revice_time == null){
                        $date = substr($row->created_at, 8,2).'/';
                        $date .= substr($row->created_at, 5,2).'/';
                        $date .= substr($row->created_at, 0,4).' ';
                        $date .= substr($row->created_at, 11,5);
                    }else{
                        $date = substr($row->orther_dc_revice_time, 8,2).'/';
                        $date .= substr($row->orther_dc_revice_time, 5,2).'/';
                        $date .= substr($row->orther_dc_revice_time, 0,4).' ';
                        $date .= substr($row->orther_dc_revice_time, 11,5);
                    }
                    return $date;
                })
                ->addColumn('action', function($row) {
                    if($row->tracking_status == 'ReturnBack'){
                        
                        return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->id.'\');">ยกเลิกส่งกลับ</button>';

                    }else if($row->tracking_status == 'ReceiveDoneReturn'){

                        $TransferDropCenters = TransferDropCenter::where('transfer_dropcenter_tracking_id', $row->id)->where('transfer_dropcenter_status', 'ReceiveDoneReturn')->get();
                        if(count($TransferDropCenters) > 0){
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        }else{
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-warning" onClick="CancelStatusWrong(\''.$row->id.'\');">ยกเลิกส่งกลับ</button>';
                        }

                    }else{
                        if($row->tracking_status == 'done' || $row->tracking_status == 'ReceiveDone'){
                            
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" onClick="addStatusWrong(\''.$row->id.'\');">แจ้งส่งกลับ</button>';
                        
                        }else{
                            
                            return '<a href="/previewTrackingBarcode/'. $row->id .'" class="btn btn-primary btn-sm"  target="blank"><i class="metismenu-icon pe-7s-note2"></i></a>'.'&nbsp;'.'<button class="btn-sm btn btn-outline-danger" disabled>แจ้งส่งกลับ</button>';
                        
                        }
                    
                    }
                })
                ->rawColumns(['action' => 'action','tracking_no' => 'tracking_no','tracking_status' => 'tracking_status'])
                ->make(true);
            }
        }
    }

    public function findsender_revice(Request $request){
        $Customer_sender_recive[] = Customer::where('id',$request->sender_id)->with('District')->with('amphure')->with('province')->get();
        $Customer_recive = Customer::where('id',$request->recive_id)->with('District')->with('amphure')->with('province')->get();
        array_push($Customer_sender_recive, $Customer_recive);
        
        return json_encode($Customer_sender_recive);
    }
}
