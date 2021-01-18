<?php

    require_once('conndb.php');

    function ThDate05($D,$param)
	{
	    $ThMonth = array ( "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.","พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.","ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค." );
	    $week = date( "w", strtotime($D) ); // ค่าวันในสัปดาห์ (0-6)
	    $months = date( "m", strtotime($D) )-1; // ค่าเดือน (1-12)
	    $day = date( "d" ,strtotime($D)  ); // ค่าวันที่(1-31)
	    $years = date( "Y",strtotime($D)  )+543; // ค่า ค.ศ.บวก 543 ทำให้เป็น ค.ศ.
	    $time = date("H:i",strtotime($D));
	    // วันที่ 12 พ.ค. 2563
		// เวลา 12.00
		if($param=="d"){
			return "วันที่  $day $ThMonth[$months] $years ";
		}elseif($param=="t"){
			return "เวลา $time ";
		}
	    
	}


	if(isset($_REQUEST['tracking_no'])){

		$tracking_no = $_REQUEST['tracking_no'];

		$sql = " SELECT trackings.*,customers.cust_name as receiver
				FROM
				trackings
				Left Join customers ON trackings.tracking_receiver_id = customers.id where tracking_no = ? "; 
 	 	$stmt = $conn->prepare($sql);
 	 	$stmt->bindParam(1, $tracking_no);
 		$stmt->execute();
 		$num_rows = $stmt->rowCount();
		if ($num_rows > 0) {
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

				if($row['tracking_status']=="Success"){
					$rs_status_img = "images/icon-check.png";
					$rs_cause_01 = "นำส่งเรียบร้อย";
					$rs_cause_02 = "ผู้รับ ".$row['receiver'];
				}else{
					$rs_status_img = "images/icon-time.png";
					$rs_cause_01 = "อยู่ระหว่างขนส่ง";
					$rs_cause_02 = "";
				}

				$json_result[] = [
					'tracking_no'=>$row['tracking_no'],
					'updated_at'=>$row['updated_at'],
					'tracking_receiver_id'=>$row['tracking_receiver_id'],
					'tracking_status'=>$row['tracking_status'],
					'rs_status_img'=>$rs_status_img,
					'rs_cause_01'=>$rs_cause_01,
					'rs_date'=>ThDate05($row['updated_at'],'d'),
					'rs_time'=>ThDate05($row['updated_at'],'t'),
					'receiver'=>$rs_cause_02,
				];
			}
			echo json_encode($json_result);
		}else{
			echo json_encode(0);
		}
	}

