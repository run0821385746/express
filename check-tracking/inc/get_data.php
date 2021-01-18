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
		$strlen = strlen($_REQUEST['tracking_no']);
		$tracking_no = substr($_REQUEST['tracking_no'], 0,15);
		$real_tracking_no = substr($_REQUEST['tracking_no'], 0,15);
		if (strpos($_REQUEST['tracking_no'], 'RTN') !== false || strpos($_REQUEST['tracking_no'], 'rtn') !== false || strpos($_REQUEST['tracking_no'], 'Rtn') !== false) {
			$status = "OR trackings_logs.tracking_no = '".$tracking_no."' AND trackings_logs.tracking_status_id = '14'";
			$tracking_no .= '(RTN)';
		}else{
			$status = '';
		}

		$sql = " 
			SELECT  
			trackings_logs.tracking_status_id,
			customers.cust_name as receiver,
			trackings_logs.tracking_cause,
			trackings_logs.tracking_date,

			(SELECT txt_desc FROM dataset_status WHERE id=trackings_logs.tracking_status_id) as tracking_status,
			(SELECT icon_img FROM dataset_status WHERE id=trackings_logs.tracking_status_id) as tracking_icon,
			(SELECT concat(drop_centers.drop_center_name,' ',provinces.name_th) FROM drop_centers left join provinces on provinces.id = drop_centers.drop_center_province WHERE drop_centers.id = trackings_logs.tracking_branch_id_dc) as dc,
			(SELECT concat(drop_centers.drop_center_name,' ',provinces.name_th) FROM drop_centers left join provinces on provinces.id = drop_centers.drop_center_province WHERE drop_centers.id = trackings_logs.tracking_branch_id_sub_dc) as dc_sub
			FROM
			trackings_logs
			Left Join customers ON trackings_logs.tracking_receiver_id = customers.id
			where trackings_logs.tracking_no = ? ".$status."
			ORDER BY trackings_logs.tracking_date DESC ";
			// die($sql);
 	 	$stmt = $conn->prepare($sql);
 	 	$stmt->bindParam(1, $tracking_no);
 		$stmt->execute();
 		$num_rows = $stmt->rowCount();
 		if($num_rows>0){
			$istuck_parcel = 0;
			$stmt1 = $conn->prepare($sql);
			$stmt1->bindParam(1, $tracking_no);
			$stmt1->execute();
		 
			while ($rowcount = $stmt1->fetch(PDO::FETCH_ASSOC)) {
				if($rowcount['tracking_status_id']==6 || $rowcount['tracking_status_id']==12){
					$istuck_parcel++;
				}
			}
 			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

 				if($row['tracking_cause']!=""){
					$tracking_cause = $row['tracking_cause'];
				}else{
					$tracking_cause = $row['dc'];
				}
				if($row['tracking_status_id']==7 || $row['tracking_status_id']==13){
					$sql = " 
						SELECT  
							tf.receive_name,
							tf.receive_relation,
							tf.photo,
							tf.signature
						FROM
							transfers tf
							Left Join trackings tk ON tk.id = tf.transfer_tracking_id
						where tk.tracking_no = ? and tf.receive_name != ''"; 
					$result = $conn->prepare($sql);
					$result->bindParam(1, $real_tracking_no);
					$result->execute();
					$num_rowsrecive = $result->rowCount();
					if($num_rowsrecive > 0){
						while ($rowsrecive = $result->fetch(PDO::FETCH_ASSOC)) {
							$tracking_cause = "ผู้รับพัสดุ : ".$rowsrecive['receive_name']."(".$rowsrecive['receive_relation'].") <span data-toggle='modal' data-target='#exampleModal' id='showreceive' title='View sending image' style='cursor:pointer;'><i class='fa fa-photo' style='font-size:20px'></i></span>";
							$image = $rowsrecive['photo'];
						}
					}
				}
				if($row['tracking_status_id']==6 || $row['tracking_status_id']==12){
					// if($istuck_parcel >= 1){
					// 	$tracking_cause = "ผู้รับพัสดุ : ".$row['receiver'].'(นำส่งอีกครั้ง) '.$istuck_parcel;
					// }else{
						$tracking_cause = "ผู้รับพัสดุ : ".$row['receiver'];
					// }
				}
				if($row['tracking_status_id']==5 || $row['tracking_status_id']==10){
					$tracking_cause = $row['dc_sub'];
				}
				if($row['tracking_status_id']==4 || $row['tracking_status_id']==11){
					$tracking_cause = $row['dc_sub'];
					// $istuck_parcel++;
				}
				if($row['tracking_status_id']==3 && $row['dc_sub']!=""){
					$tracking_cause = "DC ปลายทาง : ".$row['dc_sub'];
				}
				if($row['tracking_status_id']==2){
					$tracking_cause = "DC ต้นทาง : ".$row['dc'];
				}

 		?>
            <li style="padding: 10px 3px; height: 110px; max-height: 110px;">
                <ul class="date">
                    <li><?=ThDate05($row['tracking_date'],'d')?></li>
                    <li><?=ThDate05($row['tracking_date'],'t')?></li>
                </ul>
                <div class="status-img"><img src="<?=$row['tracking_icon']?>"></div>
                <ul class="status">
					<?php
						if($row['tracking_status_id']==6 || $row['tracking_status_id']==12){
							// if($istuck_parcel > 1){
							// 	echo "<li>".$row['tracking_status'];
							// 	$istuck_parcel--;
							// }else{
								// echo "<li>".$row['tracking_status']." <span data-toggle='modal' data-target='#exampleModal' id='showreceive' title='View sending image' style='cursor:pointer;'><i class='fa fa-phone' style='font-size:20px'></i>รายการติดต่อ</span></li>";
							// }
							if($row['tracking_cause'] !== NULL){
								echo "<li>".$row['tracking_status']." <span data-toggle='modal' data-target='#exampleModal_call_reccord".$row['tracking_cause']."' title='View contact record' style='cursor:pointer; color:#3498DB;'><i class='fa fa-clock-o' style='font-size:16px'></i>ประวัติการติดต่อ</span></li>";
								$sql = "SELECT  
											b.note,
											b.oncall,
											b.ontalk,
											b.callTime
										FROM
											transfers a
											LEFT JOIN courier_calls b ON b.tranfer_id = a.id AND b.courier_id = a.transfer_courier_id AND b.tracking_id = a.transfer_tracking_id
										WHERE a.id = ?
										ORDER BY b.created_at ASC ";
									// die($sql);
								$call_reccord = $conn->prepare($sql);
								$call_reccord->bindParam(1, $row['tracking_cause']);
								$call_reccord->execute();
								// $call_row = $call_reccord->fetch(PDO::FETCH_ASSOC);
								// var_dump($call_row);

								$html_return = '<table width="100%">';
								$html_return .= '<tr>';
									$html_return .= '<th>เหตุผล</th>';
									$html_return .= '<th>รอสาย/วินาที/</th>';
									$html_return .= '<th>คุยสาย/วินาที</th>';
									$html_return .= '<th>เวลาติดต่อ</th>';
								$html_return .= '</tr>';
								while ($call_row = $call_reccord->fetch(PDO::FETCH_ASSOC)) {
									$html_return .= '<tr>';
										$html_return .= '<td>'.$call_row['note'].'</td>';
										$html_return .= '<td align=\'center\'>'.$call_row['oncall'].'</td>';
										$html_return .= '<td align=\'center\'>'.$call_row['ontalk'].'</td>';
										$html_return .= '<td>'.date("d/m/Y H:i", strtotime($call_row['callTime'])).'</td>';
									$html_return .= '</tr>';
								}
								$html_return .= '</table>';
							?>
								<div class="modal fade" id="exampleModal_call_reccord<?= $row['tracking_cause'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLabel"><i class='fa fa-clock-o' style='font-size:16px'></i>ประวัติการติดต่อ</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<?= $html_return; ?>
											</div>
										</div>
									</div>
								</div>
							<?php
							}else{
								echo "<li>".$row['tracking_status']."</li>";
							}
						}else{
							echo "<li>".$row['tracking_status']."</li>";
						}
					?>
                    <li><?=($tracking_cause!=""?$tracking_cause:'&nbsp;')?></li>
                </ul>
            </li> 
 		<?php

 		  }
 		}else{
 			?>
            	<center>
                    <div style="color:red;">-- ไม่พบข้อมูล --</div>
                </center>

<?php
 		}
		
	}
?>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">รูปภาพการส่งพัสดุ</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<img src="data:image/jpeg;base64,<?= $image; ?>" width="100%" />
			</div>
		</div>
	</div>
</div>

