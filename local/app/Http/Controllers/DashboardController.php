<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\DropCenter;
use App\Model\Employee;
use App\Model\Tracking;
use App\Model\Transfer;
use App\Model\TranserBill;
use App\Model\SubTracking;
use App\Model\TransferDropCenter;
use App\Model\TransferDropCenterBill;
use App\Model\TrackingsLog;
use App\Model\Customer;
use App\Model\PostCode;
use App\Model\CourierArea;
use App\Model\TranferDropCenterDuplicate;
use App\Model\ReciveTranferDropCenterDuplicate;
use App\Model\TransfersDuplicate;
use App\Model\Booking;
use App\Model\CourierCall;
use App\Model\ReturnParcel;
use App\Model\PacelCare;
use App\Model\TrackCustomer;
use App\Model\TrackingClearDay;
use App\Model\OwnerDashboard;
use DB;
use Validator;
use PDF;
use Auth;
use DataTables;

class DashboardController extends Controller {
    public function track_result(Request $request){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $date = date('Y-m-d');
        $cutoffTime = date('Y-m-d').' 15:01:00';

        $date_next = date('Y-m-d').' 00:00:00';

        if($employee->emp_branch_id !== ''){
            $sql = "
                SELECT
                    count(a.id) AS count
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                WHERE
                    b.booking_branch_id = '$employee->emp_branch_id' AND a.tracking_status = 'done' AND a.tracking_no != '' 
                    OR b.booking_branch_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReceiveDoneReturn'
                    OR b.booking_branch_id = '$employee->emp_branch_id' AND a.tracking_status = 'transferDCDoing'
                    OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'transferDoing'
                
                    OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReceiveDone'
                    OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'transferDoing'
                    OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReturnBack'
                order by
                    a.created_at Desc
            ";
            $tracking = DB::select($sql);
            $CLS = '"CLS":"'.$tracking[0]->count.'"';
            $o_CLS = $tracking[0]->count;

            $sql = "
                SELECT
                    count(track_id) AS count
                FROM
                    cons a
                    left join parcel_wrongs b on b.id = (select c.id from parcel_wrongs c WHERE c.wrong_tracking_id = a.track_id AND c.wrong_status = 'true')
                    left join transfers d on d.id = (select e.id from transfers e WHERE e.transfer_tracking_id = a.track_id AND e.transfer_branch_id = '$employee->emp_branch_id' AND DATE(e.created_at) = CURDATE() ORDER BY id DESC LIMIT 1)
                    left join trackings f on f.id = a.track_id
                WHERE
                    a.recive_dc = '$employee->emp_branch_id' AND a.to_dc = '$employee->emp_branch_id' AND a.track_status = 'done' AND a.track_tracking_no != '' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND f.send_pick_time IS NULL
                    OR a.recive_dc = '$employee->emp_branch_id' AND a.to_dc = '$employee->emp_branch_id' AND a.track_status = 'transferDoing'
                    OR a.recive_dc = '$employee->emp_branch_id' AND a.to_dc = '$employee->emp_branch_id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND f.send_pick_time IS NULL
                    OR a.recive_dc = '$employee->emp_branch_id' AND a.to_dc != '$employee->emp_branch_id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND f.send_pick_time IS NULL

                    OR a.recive_dc != '$employee->emp_branch_id' AND a.to_dc = '$employee->emp_branch_id' AND a.track_status = 'ReceiveDone' AND a.transfer_id IS NULL AND '$date_next' > a.track_create_at AND f.send_pick_time IS NULL
                    OR a.recive_dc != '$employee->emp_branch_id' AND a.to_dc = '$employee->emp_branch_id' AND a.track_status = 'transferDoing'

                    OR a.recive_dc != '$employee->emp_branch_id' AND a.to_dc = '$employee->emp_branch_id' AND a.track_status = 'ReturnBack' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND f.send_pick_time IS NULL

                    OR a.recive_dc != '$employee->emp_branch_id' AND a.to_dc = '$employee->emp_branch_id' AND a.track_status = 'ReturnBack' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                    OR a.recive_dc = '$employee->emp_branch_id' AND a.to_dc = '$employee->emp_branch_id' AND a.track_status = 'done' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                    OR a.recive_dc = '$employee->emp_branch_id' AND a.to_dc = '$employee->emp_branch_id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                    OR a.recive_dc = '$employee->emp_branch_id' AND a.to_dc != '$employee->emp_branch_id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                    OR a.recive_dc != '$employee->emp_branch_id' AND a.to_dc = '$employee->emp_branch_id' AND a.track_status = 'ReceiveDone' AND a.transfer_id IS NULL AND '$date_next' > a.track_create_at AND DATE(f.send_pick_time) < CURDATE()
            ";
            // dd($sql);
            
            $tracking = DB::select($sql);
            $CONS = '"CONS":"'.$tracking[0]->count.'"';
            $o_CONS = $tracking[0]->count;

            $sql = "
            SELECT
                count(a.id) count
            FROM
                trackings a
                LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
            WHERE
                b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'CustomerResiveDone' AND a.updated_at like '$date%'
                OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'CustomerResiveDone' AND a.updated_at like '$date%'
                OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'CustomerResiveDoneReturn' AND a.updated_at like '$date%'
                OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id != '$employee->emp_branch_id' AND a.tracking_status = 'CustomerResiveDoneReturn' AND a.updated_at like '$date%'
            order by
                a.created_at Desc
            ";
            $tracking = DB::select($sql);
            $POD = '"POD":"'.$tracking[0]->count.'"';
            $o_POD = $tracking[0]->count;

            $sql = "SELECT 
                        count(a.id) as count
                    from 
                        transfers a 
                        LEFT JOIN transer_bills b ON a.transfer_bill_id = b.id 
                    where 
                            a.transfer_status = 'TransferToCourier' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$employee->emp_branch_id'
                            AND (
                                        SELECT 
                                            COUNT(c.id) 
                                        FROM 
                                            courier_calls c
                                        WHERE 
                                            c.tranfer_id = a.id 
                                            AND c.callstatus = '2'
                                    ) >= 3
                        OR
                            a.transfer_status = 'TransferToCourierReturn' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$employee->emp_branch_id'
                            AND (
                                        SELECT 
                                            COUNT(c.id) 
                                        FROM 
                                            courier_calls c
                                        WHERE 
                                            c.tranfer_id = a.id 
                                            AND c.callstatus = '2'
                                    ) >= 3
                        OR
                            a.transfer_status = 'TransferToCourier' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$employee->emp_branch_id'
                            AND (
                                        SELECT 
                                            COUNT(d.id) 
                                        FROM 
                                            courier_calls d
                                        WHERE 
                                            d.tranfer_id = a.id 
                                            AND d.callstatus = '1'
                                    ) >= 1
                        OR
                            a.transfer_status = 'TransferToCourierReturn' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$employee->emp_branch_id'
                            AND (
                                        SELECT 
                                            COUNT(d.id) 
                                        FROM 
                                            courier_calls d
                                        WHERE 
                                            d.tranfer_id = a.id 
                                            AND d.callstatus = '1'
                                    ) >= 1
                    
            ";
            
            $tracking = DB::select($sql);
            $DLY = '"DLY":"'.$tracking[0]->count.'"';
            $o_DLY = $tracking[0]->count;
            
            $sql = "
            SELECT
                sum(IF(a.transfer_status like '%Return', (SELECT SUM(d.subtracking_price) AS price FROM sub_trackings d WHERE subtracking_tracking_id = b.id), a.cod_amount)) codsum
            FROM
                trackings b
                LEFT JOIN transfers a ON b.id = a.transfer_tracking_id  AND a.transfer_status != 'ReturnBackToDC'
            WHERE
                a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status = 'CustomerResiveDone' AND a.created_at like '$date%'
                or a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status = 'CustomerResiveDoneReturn' AND a.created_at like '$date%'
            order by
                a.created_at Desc
            ";
            
            $tracking = DB::select($sql);
            $COD = '"COD":"'.number_format($tracking[0]->codsum,2).'"';
            $o_COD = number_format($tracking[0]->codsum,2);
            
            $sql = "
            SELECT
                sum(IF(a.transfer_status like '%Return', (SELECT SUM(d.subtracking_price) AS price FROM sub_trackings d WHERE subtracking_tracking_id = b.id), a.cod_amount)) codsum
            FROM
                trackings b
                LEFT JOIN transfers a ON a.id = (SELECT c.id FROM transfers c where c.transfer_status != 'ReturnBackToDC' AND c.transfer_tracking_id = b.id ORDER BY c.created_at DESC LIMIT 1)
            WHERE
                a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status != 'ReturnBackToDC' AND a.created_at like '$date%'
            order by
                a.created_at Desc
            ";
            $tracking = DB::select($sql);
            $COD_ALL = '"COD_ALL":"'.number_format($tracking[0]->codsum,2).'"';
            $o_COD_ALL = number_format($tracking[0]->codsum,2);

            $cutoffTime = date('Y-m-d').' 15:01:00';
            $sql = "
            SELECT
                count(a.id) count
            FROM
                trackings a
                LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                LEFT JOIN parcel_wrongs e ON a.id = e.wrong_tracking_id and e.wrong_status = 'true'
            WHERE
                b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id != '$employee->emp_branch_id' AND a.tracking_status = 'done' AND a.tracking_no != '' 
                OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id != '$employee->emp_branch_id' AND a.tracking_status = 'transferDCDoing'
                    
                OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReturnBack'
                OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'transferDCDoingReturn'
            ";
            $tracking = DB::select($sql);
            $LH = '"LH":"'.$tracking[0]->count.'"';
            $o_LH = $tracking[0]->count;
            
            $sql = "
            SELECT
                count(a.id) count
            FROM
                trackings a
                LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                LEFT JOIN parcel_wrongs e ON a.id = e.wrong_tracking_id and e.wrong_status = 'true'
            WHERE
                b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id != '$employee->emp_branch_id' AND a.tracking_status = 'TransferToDropCenter'
                OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'TransferToDropCenterReturn'
            ";
            $tracking = DB::select($sql);
            $on_LH = '"on_LH":"'.$tracking[0]->count.'"';
            $o_on_LH = $tracking[0]->count;

            $sql = "
            SELECT
                count(a.id) count
            FROM
                trackings b
                LEFT JOIN transfers a ON a.id = (SELECT c.id FROM transfers c where c.transfer_tracking_id = b.id ORDER BY c.created_at DESC LIMIT 1)
            WHERE
                a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status = 'TransferToCourier' AND a.created_at like '$date%'
                or a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status = 'TransferToCourier'
                or a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status = 'TransferToCourierReturn' AND a.created_at like '$date%'
                or a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status = 'TransferToCourierReturn'
            order by
                a.created_at Desc
            ";
            $tracking = DB::select($sql);
            $DVL = '"DVL":"'.$tracking[0]->count.'"';
            $o_DVL = $tracking[0]->count;

            $TranserBills = TranserBill::where('tranfer_bill_branch_id',$employee->emp_branch_id)
                                        ->where('transfer_bill_status', '!=', 'done')
                                        ->orderby('transfer_bill_status','Desc')
                                        ->get();
            
            $tranfer_bill = '"tranfer_bill":"'.count($TranserBills).'"';
            $o_tranfer_bill = count($TranserBills);

            // if($employee->emp_branch_id !== ''){
            //     $OwnerDashboard = OwnerDashboard::where('drop_center_id', $employee->emp_branch_id)->first();
            //     if(!empty($OwnerDashboard)){
            //         $OwnerDashboard->update([
            //             'cls' => $o_CLS,
            //             'cons' => $o_CONS,
            //             'pod' => $o_POD,
            //             'dly' => $o_DLY,
            //             'cod' => $o_COD,
            //             'cod_all' => $o_COD_ALL,
            //             'lh' => $o_LH,
            //             'on_lh' => $o_on_LH,
            //             'dvl' => $o_DVL,
            //             'tranfer_bill' => $o_tranfer_bill
            //         ]);
            //     }else{
            //         $OwnerDashboard = OwnerDashboard::create([
            //             'drop_center_id' => $employee->emp_branch_id, 
            //             'cls' => $o_CLS,
            //             'cons' => $o_CONS,
            //             'pod' => $o_POD,
            //             'dly' => $o_DLY,
            //             'cod' => $o_COD,
            //             'cod_all' => $o_COD_ALL,
            //             'lh' => $o_LH,
            //             'on_lh' => $o_on_LH,
            //             'dvl' => $o_DVL,
            //             'tranfer_bill' => $o_tranfer_bill
            //         ]);
            //     }
            // }
        
            return '{'.$CLS.','.$CONS.','.$POD.','.$DLY.','.$COD.','.$COD_ALL.','.$LH.','.$on_LH.','.$DVL.','.$tranfer_bill.'}';
        }else{
            $CLS_sum = 0;
            $CONS_sum = 0;
            $POD_sum = 0;
            $DLY_sum = 0;
            $COD_sum = 0;
            $COD_ALL_sum = 0;
            $LH_sum = 0;
            $on_LH_sum = 0;
            $DVL_sum = 0;
            $tranfer_bill_sum = 0;
            $DropCenters = DropCenter::get();
            foreach ($DropCenters as $key => $DropCenter) {
                $sql = "
                    SELECT
                        count(a.id) AS count
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                    WHERE
                        b.booking_branch_id = '$DropCenter->id' AND a.tracking_status = 'done' AND a.tracking_no != '' 
                        OR b.booking_branch_id = '$DropCenter->id' AND a.tracking_status = 'ReceiveDoneReturn'
                        OR b.booking_branch_id = '$DropCenter->id' AND a.tracking_status = 'transferDCDoing'
                        OR b.booking_branch_id = '$DropCenter->id' AND d.drop_center_id = '$DropCenter->id' AND a.tracking_status = 'transferDoing'
                    
                        OR b.booking_branch_id != '$DropCenter->id' AND d.drop_center_id = '$DropCenter->id' AND a.tracking_status = 'ReceiveDone'
                        OR b.booking_branch_id != '$DropCenter->id' AND d.drop_center_id = '$DropCenter->id' AND a.tracking_status = 'transferDoing'
                        OR b.booking_branch_id != '$DropCenter->id' AND d.drop_center_id = '$DropCenter->id' AND a.tracking_status = 'ReturnBack'
                    order by
                        a.created_at Desc
                ";
                $tracking = DB::select($sql);
                $CLS_sum += $tracking[0]->count;

                $sql = "
                    SELECT
                        count(track_id) AS count
                    FROM
                        cons a
                        left join parcel_wrongs b on b.id = (select c.id from parcel_wrongs c WHERE c.wrong_tracking_id = a.track_id AND c.wrong_status = 'true')
                        left join transfers d on d.id = (select e.id from transfers e WHERE e.transfer_tracking_id = a.track_id AND e.transfer_branch_id = '$DropCenter->id' AND DATE(e.created_at) = CURDATE() ORDER BY id DESC LIMIT 1)
                        left join trackings f on f.id = a.track_id
                    WHERE
                        a.recive_dc = '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'done' AND a.track_tracking_no != '' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND f.send_pick_time IS NULL
                        OR a.recive_dc = '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'transferDoing'
                        OR a.recive_dc = '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND f.send_pick_time IS NULL
                        OR a.recive_dc = '$DropCenter->id' AND a.to_dc != '$DropCenter->id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND f.send_pick_time IS NULL

                        OR a.recive_dc != '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReceiveDone' AND a.transfer_id IS NULL AND '$date_next' > a.track_create_at AND f.send_pick_time IS NULL
                        OR a.recive_dc != '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'transferDoing'

                        OR a.recive_dc != '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReturnBack' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND f.send_pick_time IS NULL

                        OR a.recive_dc != '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReturnBack' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                        OR a.recive_dc = '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'done' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                        OR a.recive_dc = '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                        OR a.recive_dc = '$DropCenter->id' AND a.to_dc != '$DropCenter->id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                        OR a.recive_dc != '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReceiveDone' AND a.transfer_id IS NULL AND '$date_next' > a.track_create_at AND DATE(f.send_pick_time) < CURDATE()
                ";
                // $sql = "
                //     SELECT
                //         count(track_id) AS count
                //     FROM
                //         cons a
                //         left join parcel_wrongs b on b.id = (select c.id from parcel_wrongs c WHERE c.wrong_tracking_id = a.track_id AND c.wrong_status = 'true')
                //         left join transfers d on d.id = (select e.id from transfers e WHERE e.transfer_tracking_id = a.track_id AND e.transfer_branch_id = '$DropCenter->id' AND DATE(e.created_at) = CURDATE() ORDER BY id DESC LIMIT 1)
                //         left join trackings f on f.id = a.track_id
                //     WHERE
                //         a.recive_dc = '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'done' AND a.track_tracking_no != '' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND f.send_pick_time IS NULL
                //         OR a.recive_dc = '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'transferDoing'
                //         OR a.recive_dc = '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND f.send_pick_time IS NULL
                //         OR a.recive_dc = '$DropCenter->id' AND a.to_dc != '$DropCenter->id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND f.send_pick_time IS NULL

                //         OR a.recive_dc != '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReceiveDone' AND a.transfer_id IS NULL AND '$date_next' > a.track_create_at AND f.send_pick_time IS NULL
                //         OR a.recive_dc != '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'transferDoing'

                //         OR a.recive_dc != '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReturnBack' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND f.send_pick_time IS NULL

                //         OR a.recive_dc != '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReturnBack' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                //         OR a.recive_dc = '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'done' AND '$date_next' > a.track_create_at AND a.transfer_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                //         OR a.recive_dc = '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                //         OR a.recive_dc = '$DropCenter->id' AND a.to_dc != '$DropCenter->id' AND a.track_status = 'ReceiveDoneReturn' AND '$date_next' > a.track_create_at AND d.id IS NULL AND a.clear_day_id IS NULL AND DATE(f.send_pick_time) = CURDATE()
                //         OR a.recive_dc != '$DropCenter->id' AND a.to_dc = '$DropCenter->id' AND a.track_status = 'ReceiveDone' AND a.transfer_id IS NULL AND '$date_next' > a.track_create_at AND DATE(f.send_pick_time) < CURDATE()
                // ";
                
                $tracking = DB::select($sql);
                $CONS_sum += $tracking[0]->count;

                $sql = "
                SELECT
                    count(a.id) count
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                WHERE
                    b.booking_branch_id = '$DropCenter->id' AND d.drop_center_id = '$DropCenter->id' AND a.tracking_status = 'CustomerResiveDone' AND a.updated_at like '$date%'
                    OR b.booking_branch_id != '$DropCenter->id' AND d.drop_center_id = '$DropCenter->id' AND a.tracking_status = 'CustomerResiveDone' AND a.updated_at like '$date%'
                    OR b.booking_branch_id = '$DropCenter->id' AND d.drop_center_id = '$DropCenter->id' AND a.tracking_status = 'CustomerResiveDoneReturn' AND a.updated_at like '$date%'
                    OR b.booking_branch_id = '$DropCenter->id' AND d.drop_center_id != '$DropCenter->id' AND a.tracking_status = 'CustomerResiveDoneReturn' AND a.updated_at like '$date%'
                order by
                    a.created_at Desc
                ";
                $tracking = DB::select($sql);
                $POD_sum += $tracking[0]->count;

                $sql = "SELECT 
                            count(a.id) as count
                        from 
                            transfers a 
                            LEFT JOIN transer_bills b ON a.transfer_bill_id = b.id 
                        where 
                                a.transfer_status = 'TransferToCourier' 
                                AND b.transfer_bill_status = 'TransferToCourier' 
                                AND b.tranfer_bill_branch_id = '$DropCenter->id'
                                AND (
                                            SELECT 
                                                COUNT(c.id) 
                                            FROM 
                                                courier_calls c
                                            WHERE 
                                                c.tranfer_id = a.id 
                                                AND c.callstatus = '2'
                                        ) >= 3
                            OR
                                a.transfer_status = 'TransferToCourierReturn' 
                                AND b.transfer_bill_status = 'TransferToCourier' 
                                AND b.tranfer_bill_branch_id = '$DropCenter->id'
                                AND (
                                            SELECT 
                                                COUNT(c.id) 
                                            FROM 
                                                courier_calls c
                                            WHERE 
                                                c.tranfer_id = a.id 
                                                AND c.callstatus = '2'
                                        ) >= 3
                            OR
                                a.transfer_status = 'TransferToCourier' 
                                AND b.transfer_bill_status = 'TransferToCourier' 
                                AND b.tranfer_bill_branch_id = '$DropCenter->id'
                                AND (
                                            SELECT 
                                                COUNT(d.id) 
                                            FROM 
                                                courier_calls d
                                            WHERE 
                                                d.tranfer_id = a.id 
                                                AND d.callstatus = '1'
                                        ) >= 1
                            OR
                                a.transfer_status = 'TransferToCourierReturn' 
                                AND b.transfer_bill_status = 'TransferToCourier' 
                                AND b.tranfer_bill_branch_id = '$DropCenter->id'
                                AND (
                                            SELECT 
                                                COUNT(d.id) 
                                            FROM 
                                                courier_calls d
                                            WHERE 
                                                d.tranfer_id = a.id 
                                                AND d.callstatus = '1'
                                        ) >= 1
                        
                ";
                
                $tracking = DB::select($sql);
                $DLY_sum += $tracking[0]->count;
                
                $sql = "
                SELECT
                    sum(IF(a.transfer_status like '%Return', (SELECT SUM(d.subtracking_price) AS price FROM sub_trackings d WHERE subtracking_tracking_id = b.id), a.cod_amount)) codsum
                FROM
                    trackings b
                    LEFT JOIN transfers a ON b.id = a.transfer_tracking_id  AND a.transfer_status != 'ReturnBackToDC'
                WHERE
                    a.transfer_branch_id = '$DropCenter->id' AND a.transfer_status = 'CustomerResiveDone' AND a.created_at like '$date%'
                    or a.transfer_branch_id = '$DropCenter->id' AND a.transfer_status = 'CustomerResiveDoneReturn' AND a.created_at like '$date%'
                order by
                    a.created_at Desc
                ";
                
                $tracking = DB::select($sql);
                $COD_sum += number_format($tracking[0]->codsum,2);
                
                $sql = "
                SELECT
                    sum(IF(a.transfer_status like '%Return', (SELECT SUM(d.subtracking_price) AS price FROM sub_trackings d WHERE subtracking_tracking_id = b.id), a.cod_amount)) codsum
                FROM
                    trackings b
                    LEFT JOIN transfers a ON a.id = (SELECT c.id FROM transfers c where c.transfer_status != 'ReturnBackToDC' AND c.transfer_tracking_id = b.id ORDER BY c.created_at DESC LIMIT 1)
                WHERE
                    a.transfer_branch_id = '$DropCenter->id' AND a.transfer_status != 'ReturnBackToDC' AND a.created_at like '$date%'
                order by
                    a.created_at Desc
                ";
                $tracking = DB::select($sql);
                $COD_ALL_sum += $tracking[0]->codsum;

                $cutoffTime = date('Y-m-d').' 15:01:00';
                $sql = "
                SELECT
                    count(a.id) count
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                    LEFT JOIN parcel_wrongs e ON a.id = e.wrong_tracking_id and e.wrong_status = 'true'
                WHERE
                    b.booking_branch_id = '$DropCenter->id' AND d.drop_center_id != '$DropCenter->id' AND a.tracking_status = 'done' AND a.tracking_no != '' 
                    OR b.booking_branch_id = '$DropCenter->id' AND d.drop_center_id != '$DropCenter->id' AND a.tracking_status = 'transferDCDoing'
                        
                    OR b.booking_branch_id != '$DropCenter->id' AND d.drop_center_id = '$DropCenter->id' AND a.tracking_status = 'ReturnBack'
                    OR b.booking_branch_id != '$DropCenter->id' AND d.drop_center_id = '$DropCenter->id' AND a.tracking_status = 'transferDCDoingReturn'
                ";
                $tracking = DB::select($sql);
                $LH_sum += $tracking[0]->count;
                
                $sql = "
                SELECT
                    count(a.id) count
                FROM
                    trackings a
                    LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                    LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                    LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                    LEFT JOIN parcel_wrongs e ON a.id = e.wrong_tracking_id and e.wrong_status = 'true'
                WHERE
                    b.booking_branch_id = '$DropCenter->id' AND d.drop_center_id != '$DropCenter->id' AND a.tracking_status = 'TransferToDropCenter'
                    OR b.booking_branch_id != '$DropCenter->id' AND d.drop_center_id = '$DropCenter->id' AND a.tracking_status = 'TransferToDropCenterReturn'
                ";
                $tracking = DB::select($sql);
                $on_LH_sum += $tracking[0]->count;

                $sql = "
                SELECT
                    count(a.id) count
                FROM
                    trackings b
                    LEFT JOIN transfers a ON a.id = (SELECT c.id FROM transfers c where c.transfer_tracking_id = b.id ORDER BY c.created_at DESC LIMIT 1)
                WHERE
                    a.transfer_branch_id = '$DropCenter->id' AND a.transfer_status = 'TransferToCourier' AND a.created_at like '$date%'
                    or a.transfer_branch_id = '$DropCenter->id' AND a.transfer_status = 'TransferToCourier'
                    or a.transfer_branch_id = '$DropCenter->id' AND a.transfer_status = 'TransferToCourierReturn' AND a.created_at like '$date%'
                    or a.transfer_branch_id = '$DropCenter->id' AND a.transfer_status = 'TransferToCourierReturn'
                order by
                    a.created_at Desc
                ";
                $tracking = DB::select($sql);
                $DVL_sum += $tracking[0]->count;

                $TranserBills = TranserBill::where('tranfer_bill_branch_id',$DropCenter->id)
                                            ->where('transfer_bill_status', '!=', 'done')
                                            ->orderby('transfer_bill_status','Desc')
                                            ->get();
                
                $tranfer_bill_sum += count($TranserBills);
            }

                $CLS = '"CLS":"'.$CLS_sum.'"';
                $CONS = '"CONS":"'.$CONS_sum.'"';
                $POD = '"POD":"'.$POD_sum.'"';
                $DLY = '"DLY":"'.$DLY_sum.'"';
                $COD = '"COD":"'.number_format($COD_sum,2).'"';
                $COD_ALL = '"COD_ALL":"'.number_format($COD_ALL_sum,2).'"';
                $LH = '"LH":"'.$LH_sum.'"';
                $on_LH = '"on_LH":"'.$on_LH_sum.'"';
                $DVL = '"DVL":"'.$DVL_sum.'"';
                $tranfer_bill = '"tranfer_bill":"'.$tranfer_bill_sum.'"';

            return '{'.$CLS.','.$CONS.','.$POD.','.$DLY.','.$COD.','.$COD_ALL.','.$LH.','.$on_LH.','.$DVL.','.$tranfer_bill.'}';
            // $sql = "SELECT sum(cls) AS cls, sum(cons) AS cons, sum(pod) AS pod, sum(dly) AS dly, sum(cod) AS cod, sum(cod_all) AS cod_all, sum(lh) AS lh, sum(on_lh) AS on_lh, sum(dvl) AS dvl, sum(tranfer_bill) AS tranfer_bill FROM owner_dashboards";
            // $result = DB::select($sql);
            // return '{"CLS": "'.$CLS.'", "CONS": "'.$CONS.'", "POD": "'.$POD.'", "DLY": "'.$DLY.'", "COD": "'.$COD.'", "COD_ALL": "'.$COD_ALL.'", "LH": "'.$LH.'", "on_LH": "'.$on_LH.'", "DVL": "'.$DVL.'", "tranfer_bill": "'.$tranfer_bill.'"}';
        }
    }

    public function commingto_dc(Request $request){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $cutoffTime = date('Y-m-d').' 15:01:00';

        $sql = "
        SELECT
            *
        FROM
            track_customers
        WHERE
            tracking_status = 'done' AND dropcenters_recive !=  '$employee->emp_branch_id' AND dropcenter_to =  '$employee->emp_branch_id' AND tracking_no != ''
            OR tracking_status = 'transferDCDoing' AND dropcenters_recive !=  '$employee->emp_branch_id' AND dropcenter_to =  '$employee->emp_branch_id'
            OR tracking_status = 'TransferToDropCenter' AND dropcenters_recive !=  '$employee->emp_branch_id' AND dropcenter_to =  '$employee->emp_branch_id'

            OR tracking_status = 'ReturnBack' AND dropcenter_to !=  '$employee->emp_branch_id' AND dropcenters_recive =  '$employee->emp_branch_id'
            OR tracking_status = 'transferDCDoingReturn' AND dropcenter_to !=  '$employee->emp_branch_id' AND dropcenters_recive =  '$employee->emp_branch_id'
            OR tracking_status = 'TransferToDropCenterReturn' AND dropcenter_to !=  '$employee->emp_branch_id' AND dropcenters_recive =  '$employee->emp_branch_id'
        ";
        // dd($sql);

        $trackings = DB::select($sql);
        $grouplist = [];
        $groupdistric = [];
        $sortaumphur = [];
        $sortDistric = [];
        $sortresult = [];
        foreach ($trackings as $key => $tracking) {
            if (count($grouplist) == 0) {
                $grouplist[] = $tracking->cust_district;
            } else {
                if (!in_array($tracking->cust_district, $grouplist)) {
                    $grouplist[] = $tracking->cust_district;
                }
            }
        }

        foreach ($grouplist as $key => $group) {
            $i = 0;
            foreach ($trackings as $key2 => $tracking) {
                if (count($groupdistric) == 0 && $group == $tracking->cust_district){
                    $groupdistric[$key][$i] = $tracking->cust_sub_district;
                    $i++;
                    $array_district = $groupdistric[$key];
                }else if($group == $tracking->cust_district && !in_array($tracking->cust_sub_district, $array_district)){
                    $groupdistric[$key][$i] = $tracking->cust_sub_district;
                    $i++;
                    $array_district = $groupdistric[$key];
                }
            }
        }

        foreach ($groupdistric as $key => $aumthure) {
            foreach ($aumthure as $key2 => $district) {
                foreach ($trackings as $key3 => $tracking) {
                    if (count($sortresult) == 0 && $district == $tracking->cust_sub_district){
                        $sortresult[$key][$key2][] = $tracking;
                    }else if($district == $tracking->cust_sub_district){
                        $sortresult[$key][$key2][] = $tracking;
                    }
                }
            }
        }

        $content = "";
            $content .= "<div class='card text-white'>";
                $content .= "<div class='card-header bg-secondary'>จำนวน CON รอบถัดไป</div>";
                $content .= "<div class='card-body' style='padding:0px;'>";
                    $content .= "<table class='table table-dark' style='margin-bottom: -5px;'>";
                        $content .= "<thead>";
                            $content .= "<tr>";
                                $content .= "<td scope='col'>";
                                    $content .= "อำเภอ";
                                $content .= "</td>";

                                $content .= "<td scope='col' align='center'>";
                                    $content .= "จำนวน/CON";
                                $content .= "</td>";

                                $content .= "<td scope='col'>";
                                    $content .= "ตำบล";
                                $content .= "</td>";

                                $content .= "<td scope='col' align='center'>";
                                    $content .= "จำนวน/CON";
                                $content .= "</td>";
                            $content .= "</tr>";
                        $content .= "</thead>";
                        $content .= "<tbody>";
                            if(count($trackings) > 0){
                                foreach ($sortresult as $key => $sortaumphur) {

                                    if(count($sortaumphur) > 1){
                                        $col = "rowspan='".count($sortaumphur)."'";
                                    }else{
                                        $col = "";
                                    }
                                    $allcon_inaumthure = 0;
                                    foreach ($sortaumphur as $key1 => $sortdistrict) {
                                            $allcon_inaumthure += count($sortdistrict);
                                    }
                                    foreach ($sortaumphur as $key1 => $sortdistrict) {

                                            $rtn = '';
                                            if (strpos($sortdistrict[0]->tracking_status, 'Return') !== false) {
                                                $rtn = '(RTN)';
                                            }
                                        if(($key%2) == 0){
                                            if($key1 == 0){
                                                $content .= "<tr style='background-color: #fff !important; color:#000;'>";
                                                    $content .= "<td ".$col.">";
                                                        $content .= $sortdistrict[0]->district_name;
                                                    $content .= "</td>";
                            
                                                    $content .= "<td ".$col." align='center'>";
                                                        $content .= $allcon_inaumthure;
                                                    $content .= "</td>";
                            
                                                    $content .= "<td>";
                                                        $content .= $sortdistrict[0]->sub_district_name.$rtn;
                                                    $content .= "</td>";
                            
                                                    $content .= "<td align='center'>";
                                                        $content .= count($sortdistrict);
                                                    $content .= "</td>";
                                                $content .= "</tr>";
                                            }else{
                                                $content .= "<tr style='background-color: #fff !important; color:#000;'>";
                            
                                                    $content .= "<td>";
                                                        $content .= $sortdistrict[0]->sub_district_name.$rtn;
                                                    $content .= "</td>";
                            
                                                    $content .= "<td align='center'>";
                                                        $content .= count($sortdistrict);
                                                    $content .= "</td>";
                                                $content .= "</tr>";
                                            }
                                        }else{
                                            if($key1 == 0){
                                                $content .= "<tr style='background-color: #ddd !important; color:#000;'>";
                                                    $content .= "<td ".$col.">";
                                                        $content .= $sortdistrict[0]->district_name;
                                                    $content .= "</td>";
                            
                                                    $content .= "<td ".$col." align='center'>";
                                                        $content .= $allcon_inaumthure;
                                                    $content .= "</td>";
                            
                                                    $content .= "<td>";
                                                        $content .= $sortdistrict[0]->sub_district_name.$rtn;
                                                    $content .= "</td>";
                            
                                                    $content .= "<td align='center'>";
                                                        $content .= count($sortdistrict);
                                                    $content .= "</td>";
                                                $content .= "</tr>";
                                            }else{
                                                $content .= "<tr style='background-color: #ddd !important; color:#000;'>";
                            
                                                    $content .= "<td>";
                                                        $content .= $sortdistrict[0]->sub_district_name.$rtn;
                                                    $content .= "</td>";
                            
                                                    $content .= "<td align='center'>";
                                                        $content .= count($sortdistrict);
                                                    $content .= "</td>";
                                                $content .= "</tr>";
                                            }
                                        }
                                    }
                                }
                            }else{
                                $content .= "<tr style='background-color: #ddd !important; color:#000;'>";
                                    $content .= "<td colspan='4' align='center'>";
                                        $content .= 'No data available in table';
                                    $content .= "</td>";
                                $content .= "</tr>";
                            }
                        $content .= "</tbody>";
                        $content .= "<tfoot>";
                            $content .= "<tr>";
                                $content .= "<td>";
                                    $content .= "รวม";
                                $content .= "</td>";
                                $content .= "<td colspan='3' align='right'>";
                                    $content .= count($trackings).' CON';
                                $content .= "</td>";
                            $content .= "</tr>";
                        $content .= "</tfoot>";
                    $content .= "</table>";
                $content .= "</div>";
            $content .= "</div>";

        return $content;
    }

    public function track_Detail_from_result(Request $request){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $date = date('Y-m-d');
        $cutoffTime = date('Y-m-d').' 15:01:00';
        if(empty($request->droup_id)){
            if($request->type == 'CLS'){
                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        g.id as senderId,
                        g.cust_name as senderName,
                        g.cust_phone as senderphone,
                        b.booking_branch_id as recive_dc,
                        d.drop_center_id as to_dc
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON d.postcode = c.cust_postcode
                        LEFT JOIN customers g ON g.id = b.booking_sender_id
                    WHERE
                        b.booking_branch_id = '$employee->emp_branch_id' AND a.tracking_status = 'done' AND a.tracking_no != '' 
                        OR b.booking_branch_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReceiveDoneReturn'
                        OR b.booking_branch_id = '$employee->emp_branch_id' AND a.tracking_status = 'transferDCDoing'
                        OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'transferDoing'
                    
                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReceiveDone'
                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'transferDoing'
                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReturnBack'
                    order by
                        a.created_at Desc
                ";
            }else if($request->type == 'CONS'){
                $date_next = date('Y-m-d').' 00:00:00';
                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        g.id as senderId,
                        g.cust_name as senderName,
                        g.cust_phone as senderphone
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON b.id = a.tracking_booking_id
                        LEFT JOIN customers c ON c.id = a.tracking_receiver_id 
                        LEFT JOIN post_codes d ON d.postcode = c.cust_postcode
                        LEFT JOIN transfers e ON e.id = (select j.id FROM transfers j where j.transfer_tracking_id = a.id AND j.transfer_branch_id = '$employee->emp_branch_id' AND DATE(j.created_at) = CURDATE() ORDER by j.id DESC LIMIT 1)
                        LEFT JOIN parcel_wrongs f ON f.wrong_tracking_id = a.id AND f.wrong_status = 'true'
                        LEFT JOIN customers g ON g.id = b.booking_sender_id
                        LEFT JOIN tracking_clear_days h ON h.id = (select i.id from tracking_clear_days i where i.tracking_id = a.id AND DATE(i.created_at) = CURDATE() ORDER BY i.created_at DESC LIMIT 1)
                    WHERE
                        b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'done' AND a.tracking_no != ''  AND '$date_next' > a.created_at AND e.id IS NULL
                        OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'transferDoing'
                        OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReceiveDoneReturn' AND '$date_next' > a.created_at AND h.id IS NULL AND e.id IS NULL
                        OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id != '$employee->emp_branch_id' AND a.tracking_status = 'ReceiveDoneReturn' AND '$date_next' > a.created_at AND h.id IS NULL AND e.id IS NULL
                    
                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReceiveDone' AND e.id IS NULL AND '$date_next' > a.created_at AND a.send_pick_time IS NULL
                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReceiveDone' AND e.id IS NULL AND '$date_next' > a.created_at AND DATE(a.send_pick_time) < CURDATE()
                        
                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'transferDoing'

                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReturnBack' AND '$date_next' > a.created_at
                    order by
                        a.created_at Desc
                ";
                // dd($sql);
                
            }else if($request->type == 'POD'){

                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        e.id as senderId,
                        e.cust_name as senderName,
                        e.cust_phone as senderphone
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                        LEFT JOIN customers e ON b.booking_sender_id = e.id
                    WHERE
                        b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'CustomerResiveDone' AND a.updated_at like '$date%'
                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'CustomerResiveDone' AND a.updated_at like '$date%'
                        OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'CustomerResiveDoneReturn' AND a.updated_at like '$date%'
                        OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id != '$employee->emp_branch_id' AND a.tracking_status = 'CustomerResiveDoneReturn' AND a.updated_at like '$date%'
                    order by
                        a.created_at Desc
                ";
                
            }else if($request->type == 'DLY'){

                $sql = "
                    SELECT 
                        e.*,
                        g.id as reciveId,
                        g.cust_name as reciveName,
                        g.cust_phone as recivephone,
                        i.id as senderId,
                        i.cust_name as senderName,
                        i.cust_phone as senderphone,
                        (select j.note from courier_calls j where j.tracking_id = e.id and j.tranfer_id = a.id order by j.id DESC limit 1) as cleardaynote
                    from 
                        transfers a 
                        LEFT JOIN transer_bills b ON a.transfer_bill_id = b.id 
                        LEFT JOIN trackings e ON e.id = a.transfer_tracking_id
                        LEFT JOIN customers g ON g.id = e.tracking_receiver_id
                        LEFT JOIN bookings h ON h.id = e.tracking_booking_id
                        LEFT JOIN customers i ON i.id = h.booking_sender_id
                    where 
                            a.transfer_status = 'TransferToCourier' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$employee->emp_branch_id'
                            AND (
                                        SELECT 
                                            COUNT(c.id) 
                                        FROM 
                                            courier_calls c
                                        WHERE 
                                            c.tranfer_id = a.id 
                                            AND c.callstatus = '2'
                                    ) >= 3
                        OR
                            a.transfer_status = 'TransferToCourierReturn' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$employee->emp_branch_id'
                            AND (
                                        SELECT 
                                            COUNT(c.id) 
                                        FROM 
                                            courier_calls c
                                        WHERE 
                                            c.tranfer_id = a.id 
                                            AND c.callstatus = '2'
                                    ) >= 3
                        OR
                            a.transfer_status = 'TransferToCourier' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$employee->emp_branch_id'
                            AND (
                                        SELECT 
                                            COUNT(d.id) 
                                        FROM 
                                            courier_calls d
                                        WHERE 
                                            d.tranfer_id = a.id 
                                            AND d.callstatus = '1'
                                    ) >= 1
                        OR
                            a.transfer_status = 'TransferToCourierReturn' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$employee->emp_branch_id'
                            AND (
                                        SELECT 
                                            COUNT(d.id) 
                                        FROM 
                                            courier_calls d
                                        WHERE 
                                            d.tranfer_id = a.id 
                                            AND d.callstatus = '1'
                                    ) >= 1
                ";

            }else if($request->type == 'COD'){

                $sql = "
                    SELECT
                        b.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        f.id as senderId,
                        f.cust_name as senderName,
                        f.cust_phone as senderphone,
                        IF(a.transfer_status like '%Return', b.tracking_amount, a.cod_amount) as cod_amount,
                        a.transfer_status,
                        a.transfer_bill_id,
                        b.tracking_status
                    FROM
                        trackings b
                        LEFT JOIN transfers a ON b.id = a.transfer_tracking_id  AND a.transfer_status != 'ReturnBackToDC'
                        LEFT JOIN customers c ON b.tracking_receiver_id = c.id
                        LEFT JOIN bookings e ON b.tracking_booking_id = e.id
                        LEFT JOIN customers f ON e.booking_sender_id = f.id
                    WHERE
                        a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status = 'CustomerResiveDone' AND a.created_at like '$date%'
                        or a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status = 'CustomerResiveDoneReturn' AND a.created_at like '$date%'
                        or a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status = 'TransferToCourier' AND a.created_at like '$date%'
                        or a.transfer_branch_id = '$employee->emp_branch_id' AND a.transfer_status = 'TransferToCourierReturn' AND a.created_at like '$date%'
                    order by
                        a.created_at Desc
                ";
            }else if($request->type == 'LH'){

                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        f.id as senderId,
                        f.cust_name as senderName,
                        f.cust_phone as senderphone
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                        LEFT JOIN parcel_wrongs e ON a.id = e.wrong_tracking_id and e.wrong_status = 'true'
                        LEFT JOIN customers f ON b.booking_sender_id = f.id
                    WHERE
                        b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id != '$employee->emp_branch_id' AND a.tracking_status = 'done' AND a.tracking_no != '' 
                        OR b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id != '$employee->emp_branch_id' AND a.tracking_status = 'transferDCDoing'
                            
                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'ReturnBack'
                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'transferDCDoingReturn'
                    order by a.created_at ASC
                ";
            }else if($request->type == 'on_LH'){

                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        f.id as senderId,
                        f.cust_name as senderName,
                        f.cust_phone as senderphone
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                        LEFT JOIN parcel_wrongs e ON a.id = e.wrong_tracking_id and e.wrong_status = 'true'
                        LEFT JOIN customers f ON b.booking_sender_id = f.id
                    WHERE
                        b.booking_branch_id = '$employee->emp_branch_id' AND d.drop_center_id != '$employee->emp_branch_id' AND a.tracking_status = 'TransferToDropCenter'
                        OR b.booking_branch_id != '$employee->emp_branch_id' AND d.drop_center_id = '$employee->emp_branch_id' AND a.tracking_status = 'TransferToDropCenterReturn'
                    order by a.created_at ASC
                ";
            }else if($request->type == 'DVL'){

                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        e.id as senderId,
                        e.cust_name as senderName,
                        e.cust_phone as senderphone,
                        b.transfer_status
                    FROM
                        trackings a
                        LEFT JOIN transfers b ON a.id = b.transfer_tracking_id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN bookings d ON a.tracking_booking_id = d.id
                        LEFT JOIN customers e ON d.booking_sender_id = e.id
                    WHERE
                        b.transfer_branch_id = '$employee->emp_branch_id' AND b.transfer_status = 'TransferToCourier' AND b.created_at like '$date%'
                        or b.transfer_branch_id = '$employee->emp_branch_id' AND b.transfer_status = 'TransferToCourier'
                        or b.transfer_branch_id = '$employee->emp_branch_id' AND b.transfer_status = 'TransferToCourierReturn' AND b.created_at like '$date%'
                        or b.transfer_branch_id = '$employee->emp_branch_id' AND b.transfer_status = 'TransferToCourierReturn'
                    order by
                        b.created_at Desc
                ";
            }
            $tracking = DB::select($sql);
            return Datatables::of($tracking)
                    ->addIndexColumn()
                    ->editColumn('tracking_no',function($row) use($request){
                        if (strpos($row->tracking_status, 'Return') !== false) {
                            if($request->type == 'CONS'){
                                if($row->tracking_status == 'ReturnBack'){
                                    $tracking_no = $row->tracking_no.'(RTN)';
                                }else{
                                    // $tracking_no = $row->tracking_no.'<a href="#" onclick="note_rtn_status(\''.$row->id.'\')">(RTN)</a>';
                                    $tracking_no = $row->tracking_no.'(RTN)';
                                }
                            }else{
                                $tracking_no = $row->tracking_no.'(RTN)';
                            }
                        }else{
                            $tracking_no = $row->tracking_no;
                        }
                        return '<a href="'.url('parcel_care/').'/'.$tracking_no.'" target="_blank">'.$tracking_no.'</a>';
                    })
                    ->editColumn('tracking_status',function($row){
                        return $row->tracking_status;
                    })
                    ->editColumn('senderName',function($row) use($employee){
                        // $cusname = '<div style="color:#199103;">ผู้ส่ง: '.$row->senderName.' '.$row->senderphone.'</div>';
                        $cusname = '<div class="row">';
                            $cusname .= '<div class="col-md-12">';
                                $cusname .= '<div id="formnote'.$row->id.'"></div>';
                            if($row->tracking_note !== NULL){
                                $cusname .= '<div id="shownote'.$row->id.'" style="background-color:#F9FFB0; border-radius:7px;"><span id="note_content'.$row->id.'">'.$row->tracking_note.'</span>&nbsp;&nbsp;<span style="cursor:pointer; color:#6495ED;" Onclick="add_tracking_note(\''.$row->id.'\',\''.$employee->id.'\',\''.$row->tracking_note.'\')"><i class="fa fa-pencil-square-o" aria-hidden="true" title="Edit note"></i></span></div>';
                            }else{
                                $cusname .= '<div id="shownote'.$row->id.'" style="background-color:#F9FFB0; border-radius:7px; display:none;"><span id="note_content'.$row->id.'">'.$row->tracking_note.'</span>&nbsp;&nbsp;<span style="cursor:pointer; color:#6495ED;" Onclick="add_tracking_note(\''.$row->id.'\',\''.$employee->id.'\',\''.$row->tracking_note.'\')"><i class="fa fa-pencil-square-o" aria-hidden="true" title="Edit note"></i></span></div>';
                            }
                            $cusname .= '</div>';
                            $cusname .= '<div class="col-md-12">';
                            if (strpos($row->tracking_status, 'Return') !== false) {
                                //senderId
                                $Customer = Customer::where('id', $row->senderId)->first();
                                if($row->tracking_note !== NULL){
                                    $cusname .= '<div style="color:#037E91;">'.$row->senderName.' '.$row->senderphone.'</div>';
                                }else{
                                    $cusname .= '<div style="color:#037E91;">'.$row->senderName.' '.$row->senderphone.'&nbsp;&nbsp;<span style="cursor:pointer; color:#6495ED;" Onclick="add_tracking_note(\''.$row->id.'\',\''.$employee->id.'\',\''.$row->tracking_note.'\')"><i class="fa fa-bullhorn" aria-hidden="true" title="Add note"></i></span></div>';
                                }
                                $cusname .= '<div style="color:#037E91;">'.$Customer->cust_address.' '.$Customer->District->name_th.' '.$Customer->amphure->name_th.' '.$Customer->province->name_th.' '.$Customer->cust_postcode.'</div>';
                            }else{
                                $Customer = Customer::where('id', $row->reciveId)->first();
                                if($row->tracking_note !== NULL){
                                    $cusname .= '<div style="color:#037E91;">'.$row->reciveName.' '.$row->recivephone.'</div>';
                                }else{
                                    $cusname .= '<div style="color:#037E91;">'.$row->reciveName.' '.$row->recivephone.'&nbsp;&nbsp;<span style="cursor:pointer; color:#6495ED;" Onclick="add_tracking_note(\''.$row->id.'\',\''.$employee->id.'\',\''.$row->tracking_note.'\')"><i class="fa fa-bullhorn" aria-hidden="true" title="Add note"></i></span></div>';
                                }
                                $cusname .= '<div style="color:#037E91;">'.$Customer->cust_address.' '.$Customer->District->name_th.' '.$Customer->amphure->name_th.' '.$Customer->province->name_th.' '.$Customer->cust_postcode.'</div>';
                            }
                            $cusname .= '</div>';
                        $cusname .= '</div>';
                        return $cusname;
                        
                    })
                    ->editColumn('tracking_status',function($row) use($request, $employee){
                        if($request->type == 'CLS'){
                            if($row->recive_dc == $employee->emp_branch_id && $row->to_dc == $employee->emp_branch_id){

                                if($row->tracking_status == 'done'){
                                    return "รอนำส่งผู้รับ";
                                }else if($row->tracking_status == 'ReceiveDoneReturn'){
                                    return "รอจ่ายคืนผู้ส่ง";
                                }else if($row->tracking_status == 'transferDCDoing'){
                                    return "ทำจ่ายสาขาปลายทาง";
                                }else if($row->tracking_status == 'transferDoing'){
                                    return "กำลังทำจ่ายCourier";
                                }else{
                                    return $request->type.$row->tracking_status;
                                }

                            }else if($row->recive_dc !== $employee->emp_branch_id && $row->to_dc == $employee->emp_branch_id){

                                if($row->tracking_status == 'ReceiveDone'){
                                    return "รอนำส่งผู้รับ";
                                }else if($row->tracking_status == 'transferDoing'){
                                    return "กำลังทำจ่ายCourier";
                                }else if($row->tracking_status == 'ReturnBack'){
                                    return "รอจ่ายคืนสาขาต้นทาง";
                                }else{
                                    return $request->type.$row->tracking_status;
                                }

                            }else if($row->recive_dc == $employee->emp_branch_id && $row->to_dc !== $employee->emp_branch_id){
                                if($row->tracking_status == 'done'){
                                    return "รอนำส่งผู้รับ";
                                }else if($row->tracking_status == 'transferDCDoing'){
                                    return "ทำจ่ายสาขาปลายทาง";
                                }else{
                                    return $request->type.$row->tracking_status;
                                }
                            }

                        }else if($request->type == 'CONS'){

                            if($row->tracking_status == 'done' || $row->tracking_status == 'ReceiveDone'){
                                return "รอนำส่งผู้รับ";
                            }else if($row->tracking_status == 'transferDoing'){
                                return "กำลังทำจ่ายCourier";
                            }else if($row->tracking_status == 'ReceiveDoneReturn'){
                                return "รอจ่ายคืนผู้ส่ง";
                            }else if($row->tracking_status == 'ReturnBack'){
                                return "รอจ่ายคืนผู้ส่ง";
                            }else{
                                return $request->type.$row->tracking_status;
                            }

                        }else if($request->type == 'POD'){

                            if($row->tracking_status == 'CustomerResiveDone'){
                                return 'ปลายทางรับพัสดุแล้ว';
                            }else if($row->tracking_status == 'CustomerResiveDoneReturn'){
                                return 'ส่งพัสดุคืนผู้รับแล้ว';
                            }else{
                                return $request->type.$row->tracking_status;
                            }
                        }else if($request->type == 'DLY'){
                            
                            if($row->tracking_status == 'ReceiveDone' || $row->tracking_status == 'done'){

                                if($row->tracking_send_status == 'postpone'){
                                    $picktime = substr($row->send_pick_time, 0,10);
                                    $date = date('Y-m-d');
                                    
                                    $date1 = date_create($picktime);
                                    $date2 = date_create($date);
                                    $diff = date_diff($date2,$date1);
                                    $pickcount = $diff->format("%R%a");
                                    
                                    if($pickcount > 0){
                                        return "เลื่อรับพัสดุ";
                                    }else{
                                        return "นำส่งไม่สำเร็จ";
                                    }
                                }else{
                                    return "นำส่งไม่สำเร็จ";
                                }

                            }else if($row->tracking_status == 'ReceiveDoneReturn'){
                                return $row->cleardaynote;
                            }else if($row->tracking_status == 'TransferToCourier'){
                                return $row->cleardaynote;;
                            }else{
                                return $request->type.$row->tracking_status;
                            }

                        }else if($request->type == 'COD'){

                            if($row->transfer_status == 'TransferToCourier'){
                                return "อยู่ระหว่างจัดส่ง";
                            }else if($row->transfer_status == 'CustomerResiveDone' || $row->transfer_status == 'CustomerResiveDoneReturn'){
                                if($row->transfer_bill_id == ""){
                                    return "ปิดPODหน้าร้าน";
                                }else{
                                    return "จัดส่งสำเร็จ";
                                }
                            }else{
                                return $request->type.$row->transfer_status;
                            }

                        }else if($request->type == 'LH'){

                            if($row->tracking_status == 'done'){
                                return "รอจ่ายให้ปลายทาง";
                            }else if($row->tracking_status == 'transferDCDoing'){
                                return "ทำจ่ายให้สาขาปลายทาง";
                            }else if($row->tracking_status == 'ReturnBack'){
                                return "รอจ่ายคืนสาขาต้นทาง";
                            }else if($row->tracking_status == 'transferDCDoingReturn'){
                                return "ทำจ่ายคืนสาขาต้นทาง";
                            }else{
                                return $request->type.$row->tracking_status;
                            }

                        }else if($request->type == 'on_LH'){

                            return "นำส่งสาขาปลายทาง";

                        }else if($request->type == 'DVL'){

                            if($row->transfer_status == 'TransferToCourier' || $row->transfer_status == 'TransferToCourierReturn'){
                                return "นำส่งตามลำดับ";
                            }else{
                                return $request->type.$row->transfer_status;
                            }

                        }
                        
                    })
                    ->editColumn('created_at',function($row) use($request){
                        if($request->type == 'COD'){
                            if($row->tracking_status == 'CustomerResiveDoneReturn'){

                                $cod = '<span style="color:#199103;">'.number_format($row->cod_amount,2).'</span>';

                            }else if($row->transfer_status == 'CustomerResiveDone'){

                                $cod = '<span style="color:#199103;">'.number_format($row->cod_amount,2).'</span>';

                            }else{

                                $cod = '<span style="color:#910323;">'.number_format($row->cod_amount,2).'</span>';

                            }
                            return $cod;
                        }else{
                            return $row->created_at;
                        }
                    })
                    ->rawColumns(['senderName' => 'senderName','created_at' => 'created_at','tracking_no' => 'tracking_no'])
                    ->make(true);
        }else{
            if($request->type == 'CLS'){
                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        g.cust_name as senderName,
                        g.cust_phone as senderphone,
                        b.booking_branch_id as recive_dc,
                        d.drop_center_id as to_dc
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON d.postcode = c.cust_postcode
                        LEFT JOIN customers g ON g.id = b.booking_sender_id
                    WHERE
                        b.booking_branch_id = '$request->droup_id' AND a.tracking_status = 'done' AND a.tracking_no != '' 
                        OR b.booking_branch_id = '$request->droup_id' AND a.tracking_status = 'ReceiveDoneReturn'
                        OR b.booking_branch_id = '$request->droup_id' AND a.tracking_status = 'transferDCDoing'
                        OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'transferDoing'
                    
                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReceiveDone'
                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'transferDoing'
                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReturnBack'
                    order by
                        a.created_at Desc
                ";
                // $sql = "
                //     SELECT
                //         a.*,
                //         c.id as reciveId,
                //         c.cust_name as reciveName,
                //         c.cust_phone as recivephone,
                //         g.cust_name as senderName,
                //         g.cust_phone as senderphone,
                //         b.booking_branch_id as recive_dc,
                //         d.drop_center_id as to_dc
                //     FROM
                //         trackings a
                //         LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                //         LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                //         LEFT JOIN post_codes d ON d.postcode = c.cust_postcode
                //         LEFT JOIN customers g ON g.id = b.booking_sender_id
                //     WHERE
                //         b.booking_branch_id = '$request->droup_id' AND a.tracking_status = 'done'
                //         OR b.booking_branch_id = '$request->droup_id' AND a.tracking_status = 'ReceiveDoneReturn'
                //         OR b.booking_branch_id = '$request->droup_id' AND a.tracking_status = 'transferDCDoing'
                //         OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'transferDoing'
                    
                //         OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReceiveDone'
                //         OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'transferDoing'
                //         OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReturnBack'
                //     order by
                //         a.created_at Desc
                // ";
            }else if($request->type == 'CONS'){
                $date_next = date('Y-m-d').' 00:00:00';
                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        g.cust_name as senderName,
                        g.cust_phone as senderphone
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON b.id = a.tracking_booking_id
                        LEFT JOIN customers c ON c.id = a.tracking_receiver_id 
                        LEFT JOIN post_codes d ON d.postcode = c.cust_postcode
                        LEFT JOIN transfers e ON e.id = (select j.id FROM transfers j where j.transfer_tracking_id = a.id AND j.transfer_branch_id = '$request->droup_id' AND DATE(j.created_at) = CURDATE() ORDER by j.id DESC LIMIT 1)
                        LEFT JOIN parcel_wrongs f ON f.wrong_tracking_id = a.id AND f.wrong_status = 'true'
                        LEFT JOIN customers g ON g.id = b.booking_sender_id
                        LEFT JOIN tracking_clear_days h ON h.id = (select i.id from tracking_clear_days i where i.tracking_id = a.id AND DATE(i.created_at) = CURDATE() ORDER BY i.created_at DESC LIMIT 1)
                    WHERE
                        b.booking_branch_id = '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'done' AND a.tracking_no != ''  AND '$date_next' > a.created_at AND e.id IS NULL
                        OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'transferDoing'
                        OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReceiveDoneReturn' AND '$date_next' > a.created_at AND h.id IS NULL AND e.id IS NULL
                        OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id != '$request->droup_id' AND a.tracking_status = 'ReceiveDoneReturn' AND '$date_next' > a.created_at AND h.id IS NULL AND e.id IS NULL
                    
                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReceiveDone' AND e.id IS NULL AND '$date_next' > a.created_at AND a.send_pick_time IS NULL
                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReceiveDone' AND e.id IS NULL AND '$date_next' > a.created_at AND DATE(a.send_pick_time) < CURDATE()
                        
                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'transferDoing'

                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReturnBack' AND '$date_next' > a.created_at
                    order by
                        a.created_at Desc
                ";
                // $sql = "
                //     SELECT
                //         a.*,
                //         c.id as reciveId,
                //         c.cust_name as reciveName,
                //         c.cust_phone as recivephone,
                //         g.cust_name as senderName,
                //         g.cust_phone as senderphone
                //     FROM
                //         trackings a
                //         LEFT JOIN bookings b ON b.id = a.tracking_booking_id
                //         LEFT JOIN customers c ON c.id = a.tracking_receiver_id 
                //         LEFT JOIN post_codes d ON d.postcode = c.cust_postcode
                //         LEFT JOIN transfers e ON e.id = (select j.id FROM transfers j where j.transfer_tracking_id = a.id AND j.transfer_branch_id = '$request->droup_id' AND DATE(j.created_at) = CURDATE() ORDER by j.id DESC LIMIT 1)
                //         LEFT JOIN parcel_wrongs f ON f.wrong_tracking_id = a.id AND f.wrong_status = 'true'
                //         LEFT JOIN customers g ON g.id = b.booking_sender_id
                //         LEFT JOIN tracking_clear_days h ON h.id = (select i.id from tracking_clear_days i where i.tracking_id = a.id AND DATE(i.created_at) = CURDATE() ORDER BY i.created_at DESC LIMIT 1)
                //     WHERE
                //         b.booking_branch_id = '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'done' AND '$date_next' > a.created_at AND e.id IS NULL
                //         OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'transferDoing'
                //         OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReceiveDoneReturn' AND '$date_next' > a.created_at AND h.id IS NULL AND e.id IS NULL
                //         OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id != '$request->droup_id' AND a.tracking_status = 'ReceiveDoneReturn' AND '$date_next' > a.created_at AND h.id IS NULL AND e.id IS NULL
                    
                //         OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReceiveDone' AND e.id IS NULL AND '$date_next' > a.created_at
                //         OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'transferDoing'

                //         OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReturnBack' AND '$date_next' > a.created_at
                //     order by
                //         a.created_at Desc
                // ";
                
            }else if($request->type == 'POD'){

                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        e.cust_name as senderName,
                        e.cust_phone as senderphone
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                        LEFT JOIN customers e ON b.booking_sender_id = e.id
                    WHERE
                        b.booking_branch_id = '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'CustomerResiveDone' AND a.updated_at like '$date%'
                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'CustomerResiveDone' AND a.updated_at like '$date%'
                        OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'CustomerResiveDoneReturn' AND a.updated_at like '$date%'
                        OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id != '$request->droup_id' AND a.tracking_status = 'CustomerResiveDoneReturn' AND a.updated_at like '$date%'
                    order by
                        a.created_at Desc
                ";
                
            }else if($request->type == 'DLY'){

                $sql = "
                    SELECT 
                        e.*,
                        g.id as reciveId,
                        g.cust_name as reciveName,
                        g.cust_phone as recivephone,
                        i.cust_name as senderName,
                        i.cust_phone as senderphone,
                        (select j.note from courier_calls j where j.tracking_id = e.id and j.tranfer_id = a.id order by j.id DESC limit 1) as cleardaynote
                    from 
                        transfers a 
                        LEFT JOIN transer_bills b ON a.transfer_bill_id = b.id 
                        LEFT JOIN trackings e ON e.id = a.transfer_tracking_id
                        LEFT JOIN customers g ON g.id = e.tracking_receiver_id
                        LEFT JOIN bookings h ON h.id = e.tracking_booking_id
                        LEFT JOIN customers i ON i.id = h.booking_sender_id
                    where 
                            a.transfer_status = 'TransferToCourier' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$request->droup_id'
                            AND (
                                        SELECT 
                                            COUNT(c.id) 
                                        FROM 
                                            courier_calls c
                                        WHERE 
                                            c.tranfer_id = a.id 
                                            AND c.callstatus = '2'
                                    ) >= 3
                        OR
                            a.transfer_status = 'TransferToCourierReturn' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$request->droup_id'
                            AND (
                                        SELECT 
                                            COUNT(c.id) 
                                        FROM 
                                            courier_calls c
                                        WHERE 
                                            c.tranfer_id = a.id 
                                            AND c.callstatus = '2'
                                    ) >= 3
                        OR
                            a.transfer_status = 'TransferToCourier' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$request->droup_id'
                            AND (
                                        SELECT 
                                            COUNT(d.id) 
                                        FROM 
                                            courier_calls d
                                        WHERE 
                                            d.tranfer_id = a.id 
                                            AND d.callstatus = '1'
                                    ) >= 1
                        OR
                            a.transfer_status = 'TransferToCourierReturn' 
                            AND b.transfer_bill_status = 'TransferToCourier' 
                            AND b.tranfer_bill_branch_id = '$request->droup_id'
                            AND (
                                        SELECT 
                                            COUNT(d.id) 
                                        FROM 
                                            courier_calls d
                                        WHERE 
                                            d.tranfer_id = a.id 
                                            AND d.callstatus = '1'
                                    ) >= 1
                ";

            }else if($request->type == 'COD'){

                $sql = "
                    SELECT
                        b.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        f.cust_name as senderName,
                        f.cust_phone as senderphone,
                        IF(a.transfer_status like '%Return', b.tracking_amount, a.cod_amount) as cod_amount,
                        a.transfer_status,
                        a.transfer_bill_id,
                        b.tracking_status
                    FROM
                        trackings b
                        LEFT JOIN transfers a ON b.id = a.transfer_tracking_id  AND a.transfer_status != 'ReturnBackToDC'
                        LEFT JOIN customers c ON b.tracking_receiver_id = c.id
                        LEFT JOIN bookings e ON b.tracking_booking_id = e.id
                        LEFT JOIN customers f ON e.booking_sender_id = f.id
                    WHERE
                        a.transfer_branch_id = '$request->droup_id' AND a.transfer_status = 'CustomerResiveDone' AND a.created_at like '$date%'
                        or a.transfer_branch_id = '$request->droup_id' AND a.transfer_status = 'CustomerResiveDoneReturn' AND a.created_at like '$date%'
                        or a.transfer_branch_id = '$request->droup_id' AND a.transfer_status = 'TransferToCourier' AND a.created_at like '$date%'
                        or a.transfer_branch_id = '$request->droup_id' AND a.transfer_status = 'TransferToCourierReturn' AND a.created_at like '$date%'
                    order by
                        a.created_at Desc
                ";
            }else if($request->type == 'LH'){

                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        f.cust_name as senderName,
                        f.cust_phone as senderphone
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                        LEFT JOIN parcel_wrongs e ON a.id = e.wrong_tracking_id and e.wrong_status = 'true'
                        LEFT JOIN customers f ON b.booking_sender_id = f.id
                    WHERE
                        b.booking_branch_id = '$request->droup_id' AND d.drop_center_id != '$request->droup_id' AND a.tracking_status = 'done' AND a.tracking_no != '' 
                        OR b.booking_branch_id = '$request->droup_id' AND d.drop_center_id != '$request->droup_id' AND a.tracking_status = 'transferDCDoing'
                            
                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'ReturnBack'
                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'transferDCDoingReturn'
                    order by a.created_at ASC
                ";
            }else if($request->type == 'on_LH'){

                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        f.cust_name as senderName,
                        f.cust_phone as senderphone
                    FROM
                        trackings a
                        LEFT JOIN bookings b ON a.tracking_booking_id = b.id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN post_codes d ON c.cust_postcode = d.postcode
                        LEFT JOIN parcel_wrongs e ON a.id = e.wrong_tracking_id and e.wrong_status = 'true'
                        LEFT JOIN customers f ON b.booking_sender_id = f.id
                    WHERE
                        b.booking_branch_id = '$request->droup_id' AND d.drop_center_id != '$request->droup_id' AND a.tracking_status = 'TransferToDropCenter'
                        OR b.booking_branch_id != '$request->droup_id' AND d.drop_center_id = '$request->droup_id' AND a.tracking_status = 'TransferToDropCenterReturn'
                    order by a.created_at ASC
                ";
            }else if($request->type == 'DVL'){

                $sql = "
                    SELECT
                        a.*,
                        c.id as reciveId,
                        c.cust_name as reciveName,
                        c.cust_phone as recivephone,
                        e.cust_name as senderName,
                        e.cust_phone as senderphone,
                        b.transfer_status
                    FROM
                        trackings a
                        LEFT JOIN transfers b ON a.id = b.transfer_tracking_id
                        LEFT JOIN customers c ON a.tracking_receiver_id = c.id
                        LEFT JOIN bookings d ON a.tracking_booking_id = d.id
                        LEFT JOIN customers e ON d.booking_sender_id = e.id
                    WHERE
                        b.transfer_branch_id = '$request->droup_id' AND b.transfer_status = 'TransferToCourier' AND b.created_at like '$date%'
                        or b.transfer_branch_id = '$request->droup_id' AND b.transfer_status = 'TransferToCourier'
                        or b.transfer_branch_id = '$request->droup_id' AND b.transfer_status = 'TransferToCourierReturn' AND b.created_at like '$date%'
                        or b.transfer_branch_id = '$request->droup_id' AND b.transfer_status = 'TransferToCourierReturn'
                    order by
                        b.created_at Desc
                ";
            }
            $tracking = DB::select($sql);
            return Datatables::of($tracking)
                    ->addIndexColumn()
                    ->editColumn('tracking_no',function($row) use($request){
                        if (strpos($row->tracking_status, 'Return') !== false) {
                            if($request->type == 'CONS'){
                                if($row->tracking_status == 'ReturnBack'){
                                    $tracking_no = $row->tracking_no.'(RTN)';
                                }else{
                                    $tracking_no = $row->tracking_no.'(RTN)';
                                    // $tracking_no = $row->tracking_no.'<a href="#" onclick="note_rtn_status(\''.$row->id.'\')">(RTN)</a>';
                                }
                            }else{
                                $tracking_no = $row->tracking_no.'(RTN)';
                            }
                        }else{
                            $tracking_no = $row->tracking_no;
                        }
                        return '<a href="'.url('parcel_care/').'/'.$tracking_no.'" target="_blank">'.$tracking_no.'</a>';
                    })
                    ->editColumn('tracking_status',function($row){
                        return $row->tracking_status;
                    })
                    ->editColumn('senderName',function($row) use($employee){
                        $Customer = Customer::where('id', $row->reciveId)->first();
                        // $cusname = '<div style="color:#199103;">ผู้ส่ง: '.$row->senderName.' '.$row->senderphone.'</div>';
                        $cusname = '<div class="row">';
                            $cusname .= '<div class="col-md-12">';
                                $cusname .= '<div id="formnote'.$row->id.'"></div>';
                            if($row->tracking_note !== NULL){
                                $cusname .= '<div id="shownote'.$row->id.'" style="background-color:#F9FFB0; border-radius:7px;"><span id="note_content'.$row->id.'">'.$row->tracking_note.'</span>&nbsp;&nbsp;<span style="cursor:pointer; color:#6495ED;" Onclick="add_tracking_note(\''.$row->id.'\',\''.$employee->id.'\',\''.$row->tracking_note.'\')"><i class="fa fa-pencil-square-o" aria-hidden="true" title="Edit note"></i></span></div>';
                            }else{
                                $cusname .= '<div id="shownote'.$row->id.'" style="background-color:#F9FFB0; border-radius:7px; display:none;"><span id="note_content'.$row->id.'">'.$row->tracking_note.'</span>&nbsp;&nbsp;<span style="cursor:pointer; color:#6495ED;" Onclick="add_tracking_note(\''.$row->id.'\',\''.$employee->id.'\',\''.$row->tracking_note.'\')"><i class="fa fa-pencil-square-o" aria-hidden="true" title="Edit note"></i></span></div>';
                            }
                            $cusname .= '</div>';
                            $cusname .= '<div class="col-md-12">';
                            if($row->tracking_note !== NULL){
                                $cusname .= '<div style="color:#037E91;">'.$row->reciveName.' '.$row->recivephone.'</div>';
                            }else{
                                $cusname .= '<div style="color:#037E91;">'.$row->reciveName.' '.$row->recivephone.'&nbsp;&nbsp;<span style="cursor:pointer; color:#6495ED;" Onclick="add_tracking_note(\''.$row->id.'\',\''.$employee->id.'\',\''.$row->tracking_note.'\')"><i class="fa fa-bullhorn" aria-hidden="true" title="Add note"></i></span></div>';
                            }
                                $cusname .= '<div style="color:#037E91;">'.$Customer->cust_address.' '.$Customer->District->name_th.' '.$Customer->amphure->name_th.' '.$Customer->province->name_th.' '.$Customer->cust_postcode.'</div>';
                            $cusname .= '</div>';
                        $cusname .= '</div>';
                        return $cusname;

                        // $cusname = '<div style="color:#037E91;">'.$row->reciveName.' '.$row->recivephone.'</div>';
                        // $cusname .= '<div style="color:#037E91;">'.$Customer->cust_address.' '.$Customer->District->name_th.' '.$Customer->amphure->name_th.' '.$Customer->province->name_th.' '.$Customer->cust_postcode.'</div>';
                        // return $cusname;
                        
                    })
                    ->editColumn('tracking_status',function($row) use($request){
                        if($request->type == 'CLS'){
                            if($row->recive_dc == $request->droup_id && $row->to_dc == $request->droup_id){

                                if($row->tracking_status == 'done'){
                                    return "รอนำส่งผู้รับ";
                                }else if($row->tracking_status == 'ReceiveDoneReturn'){
                                    return "รอจ่ายคืนผู้ส่ง";
                                }else if($row->tracking_status == 'transferDCDoing'){
                                    return "ทำจ่ายสาขาปลายทาง";
                                }else if($row->tracking_status == 'transferDoing'){
                                    return "กำลังทำจ่ายCourier";
                                }else{
                                    return $request->type."'".$row->tracking_status."'";
                                }

                            }else if($row->recive_dc !== $request->droup_id && $row->to_dc == $request->droup_id){

                                if($row->tracking_status == 'ReceiveDone'){
                                    return "รอนำส่งผู้รับ";
                                }else if($row->tracking_status == 'transferDoing'){
                                    return "กำลังทำจ่ายCourier";
                                }else if($row->tracking_status == 'ReturnBack'){
                                    return "รอจ่ายคืนสาขาต้นทาง";
                                }else{
                                    return $request->type.$row->tracking_status;
                                }

                            }else if($row->recive_dc == $request->droup_id && $row->to_dc !== $request->droup_id){
                                if($row->tracking_status == 'done'){
                                    return "รอนำส่งผู้รับ";
                                }else if($row->tracking_status == 'transferDCDoing'){
                                    return "ทำจ่ายสาขาปลายทาง";
                                }else if($row->tracking_status == 'ReceiveDoneReturn'){
                                    return "รอจ่ายคืนผู้ส่ง";
                                }else{
                                    return $request->type.$row->tracking_status;
                                }
                            }

                        }else if($request->type == 'CONS'){

                            if($row->tracking_status == 'done' || $row->tracking_status == 'ReceiveDone'){
                                return "รอนำส่งผู้รับ";
                            }else if($row->tracking_status == 'transferDoing'){
                                return "กำลังทำจ่ายCourier";
                            }else if($row->tracking_status == 'ReceiveDoneReturn'){
                                return "รอจ่ายคืนผู้ส่ง";
                            }else if($row->tracking_status == 'ReturnBack'){
                                return "รอจ่ายคืนผู้ส่ง";
                            }else{
                                return $request->type.$row->tracking_status;
                            }

                        }else if($request->type == 'POD'){

                            if($row->tracking_status == 'CustomerResiveDone'){
                                return 'ปลายทางรับพัสดุแล้ว';
                            }else if($row->tracking_status == 'CustomerResiveDoneReturn'){
                                return 'ส่งพัสดุคืนผู้รับแล้ว';
                            }else{
                                return $request->type.$row->tracking_status;
                            }
                        }else if($request->type == 'DLY'){
                            
                            if($row->tracking_status == 'ReceiveDone' || $row->tracking_status == 'done'){

                                if($row->tracking_send_status == 'postpone'){
                                    $picktime = substr($row->send_pick_time, 0,10);
                                    $date = date('Y-m-d');
                                    
                                    $date1 = date_create($picktime);
                                    $date2 = date_create($date);
                                    $diff = date_diff($date2,$date1);
                                    $pickcount = $diff->format("%R%a");
                                    
                                    if($pickcount > 0){
                                        return "เลื่อรับพัสดุ";
                                    }else{
                                        return "นำส่งไม่สำเร็จ";
                                    }
                                }else{
                                    return "นำส่งไม่สำเร็จ";
                                }

                            }else if($row->tracking_status == 'ReceiveDoneReturn'){
                                return $row->cleardaynote;
                            }else if($row->tracking_status == 'TransferToCourier'){
                                return $row->cleardaynote;;
                            }else{
                                return $request->type.$row->tracking_status;
                            }

                        }else if($request->type == 'COD'){

                            if($row->transfer_status == 'TransferToCourier'){
                                return "อยู่ระหว่างจัดส่ง";
                            }else if($row->transfer_status == 'CustomerResiveDone' || $row->transfer_status == 'CustomerResiveDoneReturn'){
                                if($row->transfer_bill_id == ""){
                                    return "ปิดPODหน้าร้าน";
                                }else{
                                    return "จัดส่งสำเร็จ";
                                }
                            }else{
                                return $request->type.$row->transfer_status;
                            }

                        }else if($request->type == 'LH'){

                            if($row->tracking_status == 'done'){
                                return "รอจ่ายให้ปลายทาง";
                            }else if($row->tracking_status == 'transferDCDoing'){
                                return "ทำจ่ายให้สาขาปลายทาง";
                            }else if($row->tracking_status == 'ReturnBack'){
                                return "รอจ่ายคืนสาขาต้นทาง";
                            }else if($row->tracking_status == 'transferDCDoingReturn'){
                                return "ทำจ่ายคืนสาขาต้นทาง";
                            }else{
                                return $request->type.$row->tracking_status;
                            }

                        }else if($request->type == 'on_LH'){

                            return "นำส่งสาขาปลายทาง";

                        }else if($request->type == 'DVL'){

                            if($row->transfer_status == 'TransferToCourier' || $row->transfer_status == 'TransferToCourierReturn'){
                                return "นำส่งตามลำดับ";
                            }else{
                                return $request->type.$row->transfer_status;
                            }

                        }
                        
                    })
                    ->editColumn('created_at',function($row) use($request){
                        if($request->type == 'COD'){
                            if($row->tracking_status == 'CustomerResiveDoneReturn'){

                                $cod = '<span style="color:#199103;">'.number_format($row->cod_amount,2).'</span>';

                            }else if($row->transfer_status == 'CustomerResiveDone'){

                                $cod = '<span style="color:#199103;">'.number_format($row->cod_amount,2).'</span>';

                            }else{

                                $cod = '<span style="color:#910323;">'.number_format($row->cod_amount,2).'</span>';

                            }
                            return $cod;
                        }else{
                            return $row->created_at;
                        }
                    })
                    ->rawColumns(['senderName' => 'senderName','created_at' => 'created_at','tracking_no' => 'tracking_no'])
                    ->make(true);
        }
    }

    public function setclearday_status(Request $request){
        $validator = Validator::make($request->all(), [
            'tracking_id' => 'required',
            'note' => 'required'
            ]);
           
        if($validator->fails()) {
            alert()->error('ขออภัย', 'กรุณากรอกข้อมูล')->showConfirmButton("ตกลง","#3085d6");
            return redirect()->back();
        }  
        $TrackingClearDay = TrackingClearDay::create([
            'tracking_id' => $request->tracking_id,
            'note' => $request->note
        ]);

        alert()->success('สำเร็จ', 'แจ้งปัญหาสำเร็จ')->showConfirmButton("ตกลง","#3085d6");
        return redirect()->back();
    }
    
    public function dvl_courier_driver_list(Request $request){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $sql = "
            SELECT
                (
                    SELECT 
                        count(d.id) 
                    FROM 
                        transfers d 
                    WHERE 
                        d.transfer_bill_id = b.id AND d.transfer_status != 'ReturnBackToDC'
                        AND d.transfer_status like 'TransferToCourier%'
                        AND (
                                SELECT 
                                    COUNT(g.id) 
                                FROM 
                                    courier_calls g 
                                WHERE 
                                    g.tranfer_id = d.id 
                                    AND g.callstatus = '2'
                            ) < 3 
                        AND (
                                SELECT 
                                    COUNT(g.id) 
                                FROM 
                                    courier_calls g 
                                WHERE 
                                    g.tranfer_id = d.id 
                                    AND g.callstatus = '2'
                            ) < 3 
                        AND (
                                SELECT 
                                    COUNT(h.id) 
                                FROM 
                                    courier_calls h 
                                WHERE 
                                    h.tranfer_id = d.id 
                                    AND h.callstatus = '1'
                            ) < 1
                        AND (
                                SELECT 
                                    COUNT(h.id) 
                                FROM 
                                    courier_calls h 
                                WHERE 
                                    h.tranfer_id = d.id 
                                    AND h.callstatus = '1'
                            ) < 1
                            
                ) AS CONS,
                (
                    SELECT 
                        count(e.id) 
                    FROM 
                        transfers e 
                    WHERE 
                        e.transfer_bill_id = b.id 
                        AND e.transfer_status = 'CustomerResiveDone'
                        or e.transfer_bill_id = b.id 
                        AND e.transfer_status = 'CustomerResiveDoneReturn'
                ) AS POD,
                (
                    SELECT 
                        count(f.id) 
                    FROM 
                        transfers f 
                    WHERE 
                        f.transfer_bill_id = b.id 
                        AND f.transfer_status = 'TransferToCourier' 
                        AND (
                                SELECT 
                                    COUNT(g.id) 
                                FROM 
                                    courier_calls g 
                                WHERE 
                                    g.tranfer_id = f.id 
                                    AND g.callstatus = '2'
                            ) >= 3 
                        OR
                            f.transfer_bill_id = b.id 
                            AND f.transfer_status = 'TransferToCourierReturn' 
                            AND (
                                    SELECT 
                                        COUNT(g.id) 
                                    FROM 
                                        courier_calls g 
                                    WHERE 
                                        g.tranfer_id = f.id 
                                        AND g.callstatus = '2'
                                ) >= 3 
                        OR 
                            f.transfer_bill_id = b.id 
                            AND f.transfer_status = 'TransferToCourier' 
                            AND (
                                    SELECT 
                                        COUNT(h.id) 
                                    FROM 
                                        courier_calls h 
                                    WHERE 
                                        h.tranfer_id = f.id 
                                        AND h.callstatus = '1'
                                ) >= 1
                        OR 
                            f.transfer_bill_id = b.id 
                            AND f.transfer_status = 'TransferToCourierReturn' 
                            AND (
                                    SELECT 
                                        COUNT(h.id) 
                                    FROM 
                                        courier_calls h 
                                    WHERE 
                                        h.tranfer_id = f.id 
                                        AND h.callstatus = '1'
                                ) >= 1
                ) AS DLY,
                (
                    select 
                        SUM(IF(i.transfer_status like '%Return', k.tracking_amount, i.cod_amount)) 
                    FROM 
                        transfers i 
                        left join trackings k on i.transfer_tracking_id = k.id
                    WHERE 
                        i.transfer_bill_id = b.id
                        and i.transfer_status != 'ReturnBackToDC'
                ) AS all_cod,
                (
                    select 
                        SUM(IF(j.transfer_status like '%Return', l.tracking_amount, j.cod_amount)) 
                    FROM 
                        transfers j 
                        left join trackings l on j.transfer_tracking_id = l.id
                    WHERE 
                        j.transfer_bill_id = b.id 
                        AND j.transfer_status = 'CustomerResiveDone'
                        or j.transfer_bill_id = b.id 
                        AND j.transfer_status = 'CustomerResiveDoneReturn'
                ) AS cod_recive,
                a.emp_firstname,
                a.emp_lastname,
                a.id AS employee_id,
                b.id AS transer_bills_id
            FROM
                employees a
                LEFT JOIN transer_bills b ON  b.id = (
                                                        select 
                                                            c.id 
                                                        FROM 
                                                            transer_bills c 
                                                        WHERE 
                                                            c.transfer_bill_courier_id = a.id 
                                                            AND c.transfer_bill_status != 'done' 
                                                        ORDER BY 
                                                            c.id DESC 
                                                        LIMIT 1
                                                    )
            WHERE 
                a.emp_branch_id = '$employee->emp_branch_id' 
                AND a.emp_position = 'พนักงานจัดส่งพัสดุ(Courier)' 
                AND b.id != ''
        ";

        $courier = DB::select($sql);

        return Datatables::of($courier)
        ->addIndexColumn()
        ->editColumn('emp_firstname',function($row) {
            return '<p onclick="ckeckcon(\'DVL_Contect\',\''.$row->employee_id.'\',\''.$row->transer_bills_id.'\')" style="cursor:pointer;">'.$row->emp_firstname.' '.$row->emp_lastname.'</p>';
        })
        ->editColumn('CONS',function($row) {
            return '<p onclick="ckeckcon(\'DVL_CON\',\''.$row->employee_id.'\',\''.$row->transer_bills_id.'\')" style="cursor:pointer;">'.$row->CONS.'</p>';
        })
        ->editColumn('POD',function($row) {
            return '<p onclick="ckeckcon(\'DVL_POD\',\''.$row->employee_id.'\',\''.$row->transer_bills_id.'\')" style="cursor:pointer;">'.$row->POD.'</p>';
        })
        ->editColumn('DLY',function($row) {
            return '<p onclick="ckeckcon(\'DVL_DLY\',\''.$row->employee_id.'\',\''.$row->transer_bills_id.'\')" style="cursor:pointer;">'.$row->DLY.'</p>';
        })
        ->editColumn('all_cod',function($row) {
            $cod = '<p onclick="ckeckcon(\'DVL_COD\',\''.$row->employee_id.'\',\''.$row->transer_bills_id.'\')" style="cursor:pointer;">'.number_format($row->cod_recive,2).'<br><span style="font-size:12px;">('.number_format($row->all_cod,2).')<span>'.'</p>';
            return $cod;
        })
        ->rawColumns(['emp_firstname' => 'emp_firstname','CONS' => 'CONS','POD' => 'POD','DLY' => 'DLY','all_cod' => 'all_cod'])
        ->make(true);
    }

    public function sender_detail_dashboard(Request $request){
        if($request->type == 'DVL_Contect'){
            $TranserBills = TranserBill::where('id', $request->bill_id)->get();
            return Datatables::of($TranserBills)
            ->editColumn('sender_name',function($row) {
                return $row->courier->emp_firstname.' '.$row->courier->emp_lastname;
            })
            ->editColumn('sander_phone',function($row) {
                return $row->courier->emp_phone;
            })
            ->editColumn('number_plate',function($row) {
                return $row->tranfer_driver_sender_numberplate;
            })
            ->editColumn('Bill_create_time',function($row) {
                return $row->created_at;
            })
            ->rawColumns(['sender_name' => 'sender_name','sander_phone' => 'sander_phone','number_plate' => 'number_plate','Bill_create_time' => 'Bill_create_time'])
            ->make(true);
        }else{
            if($request->type == 'DVL_CON'){
                $sql = "SELECT 
                            d.id,
                            e.tracking_no,
                            f.cust_name,
                            f.cust_phone,
                            e.updated_at,
                            d.transfer_status,
                            k.cust_name AS cust_name_sent,
                            k.cust_phone AS cust_phone_sent
                        FROM 
                            transfers d
                            left join trackings e ON e.id = d.transfer_tracking_id
                            left join customers f ON f.id = e.tracking_receiver_id
                            left join bookings j ON j.id = e.tracking_booking_id
                            left join  customers k ON k.id = j.booking_sender_id
                        WHERE 
                            d.transfer_bill_id = '$request->bill_id' 
                            AND d.transfer_status != 'ReturnBackToDC'
                            AND d.transfer_status like 'TransferToCourier%'
                            AND (
                                    SELECT 
                                        COUNT(g.id) 
                                    FROM 
                                        courier_calls g 
                                    WHERE 
                                        g.tranfer_id = d.id 
                                        AND g.callstatus = '2'
                                ) < 3 
                            AND (
                                    SELECT 
                                        COUNT(g.id) 
                                    FROM 
                                        courier_calls g 
                                    WHERE 
                                        g.tranfer_id = d.id 
                                        AND g.callstatus = '2'
                                ) < 3 
                            AND (
                                    SELECT 
                                        COUNT(h.id) 
                                    FROM 
                                        courier_calls h 
                                    WHERE 
                                        h.tranfer_id = d.id 
                                        AND h.callstatus = '1'
                                ) < 1
                            AND (
                                    SELECT 
                                        COUNT(h.id) 
                                    FROM 
                                        courier_calls h 
                                    WHERE 
                                        h.tranfer_id = d.id 
                                        AND h.callstatus = '1'
                                ) < 1";
                $PDO = DB::select($sql);
                return Datatables::of($PDO)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row) {
                    if (strpos($row->transfer_status, 'Return') !== false) {
                        $tracking = $row->tracking_no.'(RTN)';
                    }else{
                        $tracking = $row->tracking_no;
                    }
                    return $tracking;
                })
                ->editColumn('recive_Name',function($row) {
                    if (strpos($row->transfer_status, 'Return') !== false) {
                        $recive = $row->cust_name_sent.' '.$row->cust_phone_sent;
                    }else{
                        $recive = $row->cust_name.' '.$row->cust_phone;
                    }
                    return $recive;
                })
                ->editColumn('action_time',function($row) {
                    return $row->updated_at;
                })
                ->rawColumns(['tracking_no' => 'tracking_no','recive_Name' => 'recive_Name','action_time' => 'action_time'])
                ->make(true);

            }else if($request->type == 'DVL_POD'){

                $Transfers = Transfer::where('transfer_bill_id', $request->bill_id)->where('transfer_status', 'CustomerResiveDone')->orwhere('transfer_bill_id', $request->bill_id)->where('transfer_status', 'CustomerResiveDoneReturn')->get();
                return Datatables::of($Transfers)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row) {
                    if (strpos($row->transfer_status, 'Return') !== false) {
                        $tracking = $row->tracking->tracking_no.'RTN';
                    }else{
                        $tracking = $row->tracking->tracking_no;
                    }
                    return $tracking;
                })
                ->editColumn('recive_Name',function($row) {
                    if (strpos($row->transfer_status, 'Return') !== false) {
                        $recive = $row->tracking->booking->customer->cust_name.' '.$row->tracking->booking->customer->cust_phone;
                    }else{
                        $recive = $row->tracking->receiver->cust_name.' '.$row->tracking->receiver->cust_phone;
                    }
                    return $recive;
                })
                ->editColumn('action_time',function($row) {
                    return $row->updated_at;
                })
                ->rawColumns(['tracking_no' => 'tracking_no','recive_Name' => 'recive_Name','action_time' => 'action_time'])
                ->make(true);

            }else if($request->type == 'DVL_DLY'){

                $sql = "SELECT 
                            f.id,
                            i.tracking_no,
                            j.cust_name,
                            j.cust_phone,
                            f.updated_at,
                            f.transfer_status,
                            l.cust_name AS cust_name_sent,
                            l.cust_phone AS cust_phone_sent
                        FROM 
                            transfers f 
                            left join trackings i ON i.id = f.transfer_tracking_id
                            left join customers j ON j.id = i.tracking_receiver_id
                            left join bookings k ON k.id = i.tracking_booking_id
                            left join customers l ON l.id = k.booking_sender_id
                        WHERE 
                            f.transfer_bill_id = '$request->bill_id' 
                            AND f.transfer_status = 'TransferToCourier' 
                            AND (
                                    SELECT 
                                        COUNT(g.id) 
                                    FROM 
                                        courier_calls g 
                                    WHERE 
                                        g.tranfer_id = f.id 
                                        AND g.callstatus = '2'
                                ) >= 3 
                            OR
                                f.transfer_bill_id = '$request->bill_id' 
                                AND f.transfer_status = 'TransferToCourierReturn' 
                                AND (
                                        SELECT 
                                            COUNT(g.id) 
                                        FROM 
                                            courier_calls g 
                                        WHERE 
                                            g.tranfer_id = f.id 
                                            AND g.callstatus = '2'
                                    ) >= 3 
                            OR 
                                f.transfer_bill_id = '$request->bill_id' 
                                AND f.transfer_status = 'TransferToCourier' 
                                AND (
                                        SELECT 
                                            COUNT(h.id) 
                                        FROM 
                                            courier_calls h 
                                        WHERE 
                                            h.tranfer_id = f.id 
                                            AND h.callstatus = '1'
                                    ) >= 1
                            OR 
                                f.transfer_bill_id = '$request->bill_id' 
                                AND f.transfer_status = 'TransferToCourierReturn' 
                                AND (
                                        SELECT 
                                            COUNT(h.id) 
                                        FROM 
                                            courier_calls h 
                                        WHERE 
                                            h.tranfer_id = f.id 
                                            AND h.callstatus = '1'
                                    ) >= 1";
                $PDO = DB::select($sql);
                return Datatables::of($PDO)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row) {
                    if (strpos($row->transfer_status, 'Return') !== false) {
                        $tracking = $row->tracking_no.'RTN';
                    }else{
                        $tracking = $row->tracking_no;
                    }
                    return $tracking;
                })
                ->editColumn('recive_Name',function($row) {
                    if (strpos($row->transfer_status, 'Return') !== false) {
                        $recive = $row->cust_name_sent.' '.$row->cust_phone_sent;
                    }else{
                        $recive = $row->cust_name.' '.$row->cust_phone;
                    }
                    return $recive;
                })
                ->editColumn('action_time',function($row) {
                    return $row->updated_at;
                })
                ->rawColumns(['tracking_no' => 'tracking_no','recive_Name' => 'recive_Name','action_time' => 'action_time'])
                ->make(true);

            }else if($request->type == 'DVL_COD'){

                //IF(a.transfer_status like '%Return', b.tracking_amount, a.cod_amount) AS COD
                $Transfers = Transfer::where('transfer_bill_id', $request->bill_id)->where('transfer_status', '!=', 'ReturnBackToDC')->get();
                return Datatables::of($Transfers)
                ->addIndexColumn()
                ->editColumn('tracking_no',function($row) {
                    if (strpos($row->transfer_status, 'Return') !== false) {
                        $tracking = $row->tracking->tracking_no.'RTN';
                    }else{
                        $tracking = $row->tracking->tracking_no;
                    }
                    return $tracking;
                })
                ->editColumn('recive_Name',function($row) {
                    if (strpos($row->transfer_status, 'Return') !== false) {
                        $recive = $row->tracking->booking->customer->cust_name.' '.$row->tracking->booking->customer->cust_phone;
                    }else{
                        $recive = $row->tracking->receiver->cust_name.' '.$row->tracking->receiver->cust_phone;
                    }
                    return $recive;
                })
                ->editColumn('action_time',function($row) {
                    if (strpos($row->transfer_status, 'Return') !== false) {
                        $COD = $row->tracking->tracking_amount;
                    }else{
                        $COD = $row->cod_amount;
                    }
                    if($row->transfer_status == 'CustomerResiveDone' || $row->transfer_status == 'CustomerResiveDoneReturn'){
                        $return = '<span style="background-color:green; color:#fff; padding-left:5px; padding-right:5px; border-radius:5px;">'.number_format($COD,2).'</span>';
                    }else{
                        $return = number_format($COD,2);
                    }
                    return $return;
                })
                ->rawColumns(['tracking_no' => 'tracking_no','recive_Name' => 'recive_Name','action_time' => 'action_time'])
                ->make(true);

            }
        }
    }

    public function Update_tracking_note(Request $request){
        $user = Auth::user();
        $employee = Employee::where('id',$user->employee_id)->first();
        $validator = Validator::make($request->all(), [
            'emplayee_id' => 'required',
            'tracking_id' => 'required',
            'note' => 'required'
        ]);
        if ($validator->fails()) {
            return '{"status":"0"}';
        }

        $note = $request->note.'<br>โดย : '.$employee->emp_firstname;

        $tracking = Tracking::find($request->tracking_id);
        $tracking->update([
            'tracking_note' => $note
        ]);

        return '{"status":"1","msg":"'.$note.'"}';


    }
}

