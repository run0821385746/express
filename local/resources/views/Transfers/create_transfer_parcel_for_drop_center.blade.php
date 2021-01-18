@extends("welcome")
@section("content")
<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header"> <span style="font-weight: normal;">Transfer To Drop Center :</span> &nbsp; {{ $dropcenter->drop_center_name_initial }}</div>
        <div class="row">
            <div class="col-lg-1 col-md-1 text-right"><br> Tracking No</div>
            <div class="col-lg-3 col-md-3"> <br>
                <div class="position-relative form-group">
                    <form action="/addTrackingToDropCenter/{{!empty($dropcenter->id) ? $dropcenter->id : null}}" method="POST">
                        <div class="input-group">
                            {{csrf_field()}}
                            @method('PUT')
                            <input type="text" class="form-control" name="tracking_no" autofocus>
                            <input type="hidden" name="dropcenter_id" value="{{!empty($dropcenter->id) ? $dropcenter->id : null}}">
                            <div class="input-group-append">
                                <button class="btn btn-primary">ค้นหา</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>  
            <div class="col-lg-8 col-md-8 text-right"><br>
                <button class="mb-2 mr-2 btn btn-primary">จำนวนTracking<span
                        class="badge badge-pill badge-light">
                        @if (!empty($transfers))
                            {{count($transfers)}}
                        @else
                            0
                        @endif
                    </span></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover" style="width: 100%;">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="10%" class="text-left">BookingNo</th>
                        <th width="8%" class="text-left">Tracking No</th>
                        <th width="8%" class="text-center">กล่องที่</th>
                        <th width="20%" class="text-left">ผู้รับพัสดุ</th>
                        <th width="10%" class="text-center">สถานะพัสดุ</th>
                        <th width="10%" class="text-center">สถานะทำจ่าย</th>
                        <th width="10%" class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rowno = 1;
                        $tranferready = 1;
                    @endphp
                    @if (!empty($transfers))
                        @foreach ($transfers as $transfer)
                            @php
                                $subtrackingarray = explode(",", $transfer->parcel_received_amount);
                                sort($subtrackingarray);
                                // dd(count($subtrackingarray));
                            @endphp
                            @for ($i = 0; $i < count($subtrackingarray); $i++)
                                @if ($i == 0)
                                    <tr>
                                        <td class="text-center">{{$rowno++}}</td>
                                        <td class="text-left">
                                            @php
                                                $booking = App\Model\Booking::where('id',$transfer->transfer_dropcenter_booking_id)->first();
                                            @endphp
                                            {{$booking->booking_no}} 
                                        </td>
                                        <td class="text-left">
                                            @if ($transfer->transfer_dropcenter_status == 'newReturn')
                                                {{$transfer->transfer_dropcenter_tracking_no}}(RTN)
                                            @else
                                                {{$transfer->transfer_dropcenter_tracking_no}}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{$subtrackingarray[$i].'/'.$transfer->parcel_amount}}
                                        </td>
                                        <td class="text-left">
                                            @php
                                                $trackingDetail = App\Model\Tracking::where('id',$transfer->transfer_dropcenter_tracking_id)->first();
                                            @endphp
                                            @if (!empty($trackingDetail))
                                                @if ($transfer->transfer_dropcenter_status == 'newReturn')
                                                    {{$trackingDetail->booking->customer->cust_name}}
                                                @else
                                                    {{$trackingDetail->receiver->cust_name}}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">{{$transfer->transfer_dropcenter_status}} </td>
                                        <td class="text-center" rowspan="{{count($subtrackingarray)}}">
                                            @if (count($subtrackingarray) == $transfer->parcel_amount)
                                                <span style="color: green">พร้อมทำจ่าย</span>
                                            @else
                                                <span style="color: rgb(248, 141, 0)">จำนวนพัสดุไม่ครบ</span>
                                                @php
                                                    $tranferready = 0;
                                                @endphp
                                            @endif
                                        </td>
                                        <td class="text-center" valign='center' rowspan="{{count($subtrackingarray)}}">
                                            <form action="/deleteParcelWhenTransferToDropCenter" method="POST">
                                                {{csrf_field()}}
                                                <input type="hidden" name="tracking_id" value="{{!empty($transfer->transfer_dropcenter_tracking_id) ? $transfer->transfer_dropcenter_tracking_id : null}}">
                                                <input type="hidden" name="transfer_id" value="{{!empty($transfer->id) ? $transfer->id : null}}">
                                                <input type="hidden" name="receive_dc_id" value="{{!empty($transfer->transfer_dropcenter_id) ? $transfer->transfer_dropcenter_id : null}}">
                                                <button class="mb-2 mr-2 border-0 btn-transition btn btn-outline-danger">ลบ</button>
                                            </form>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="text-center">{{$rowno++}}</td>
                                        <td class="text-left">
                                            @php
                                                $booking = App\Model\Booking::where('id',$transfer->transfer_dropcenter_booking_id)->first();
                                            @endphp
                                            {{$booking->booking_no}} 
                                        </td>
                                        <td class="text-left">{{$transfer->transfer_dropcenter_tracking_no}} </td>
                                        <td class="text-center">
                                            {{$subtrackingarray[$i].'/'.$transfer->parcel_amount}}
                                        </td>
                                        <td class="text-left">
                                            @php
                                                $trackingDetail = App\Model\Tracking::where('id',$transfer->transfer_dropcenter_tracking_id)->first();
                                            @endphp
                                            @if (!empty($trackingDetail))
                                                {{$trackingDetail->receiver->cust_name}}
                                            @endif
                                        </td>
                                        <td class="text-center">{{$transfer->transfer_dropcenter_status}} </td>
                                    </tr>
                                @endif
                                
                            @endfor
                        @endforeach
                    @endif
                </tbody>
            </table> <br><br>
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card-body">
                        <form action="/saveTransferToDropCenter" method="POST">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-lg-3 col-md-12"></div>
                                <div class="col-lg-3 col-md-4">
                                    <select name="tranfer_driver_sender_name" id="tranfer_driver_sender_name" class="form-control mb-2 mr-sm-2" onchange="check_input_submit()" required>
                                        <option value="">--กรุณาเลือกผู้ขับ--</option>
                                        @foreach ($linehauls as $linehaul)
                                            <option value="{{$linehaul->id}}">{{$linehaul->emp_firstname.' '.$linehaul->emp_lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-4">
                                    <input type="text" class="form-control mb-2 mr-sm-2" name="tranfer_driver_sender_numberplate" id="tranfer_driver_sender_numberplate" onkeyup="check_input_submit()" placeholder="ทะเบียนรถผู้ส่งสินค้า" required>
                                </div>
                                <div class="col-lg-3 col-md-4">
                                    <input type="text" class="form-control mb-2 mr-sm-2" name="tranfer_driver_sender_phone" id="tranfer_driver_sender_phone" onkeyup="check_input_submit()" placeholder="เบอร์มือถือผู้ส่งสินค้า" required>
                                </div>
                            </div>
                            <input type="hidden" name="id" id="id" value="{{$dropcenter->id}}">
                            <button style="display: none;" id='submitsavetranferform'></button>
                        </form>
                        <button type="button" data-toggle="modal" data-target="#exampleModalCenter" class="mt-1 btn btn-success">พิมพ์ใบ Line Hall</button>
                        <a href="/getDropCenterList">
                            <button class="mt-1 btn btn-light pull-right">กลับ</button>
                        </a>
                        @if ($tranferready == 0)
                            <button type="button" class="mt-1 btn btn-primary pull-right" style='margin-right:5px;' id="submitsavetransfer" disabled>บันทึกรายการ</button>
                        @else
                            <button type="button" class="mt-1 btn btn-primary pull-right" style='margin-right:5px;' id="submitsavetransfer">บันทึกรายการ</button>
                        @endif
                        
                        <div style="position:fixed; bottom:0px; right:10px; width:300px;" class="card">
                            <div class="card-header bg-primary" id="duplicates_tap">
                                รายการแสกนซ้ำ
                            </div>
                            <div class="card-body" style="padding:0px; height:200px; overflow:auto;" id="duplicates_body">
                                @if (count($TranferDropCenterDuplicates) > 0)
                                    <ul class="list-group">
                                        @foreach ($TranferDropCenterDuplicates as $TranferDropCenterDuplicate)
                                            <li class="list-group-item">
                                                <b style="color:red;">{{$TranferDropCenterDuplicate->duplicate_tracking_no}}</b>
                                                <br>
                                                <small>
                                                    {{date_format($TranferDropCenterDuplicate->created_at,"d/m/Y H:i:s")}}
                                                </small>
                                                <span class="pull-right">
                                                    @if ($TranferDropCenterDuplicate->duplicate_status == '1')
                                                        <small>รายการซ้ำ</small>
                                                    @elseif($TranferDropCenterDuplicate->duplicate_status == '2')
                                                        <small>ปลายทางไม่ถูกต้อง</small>
                                                    @elseif($TranferDropCenterDuplicate->duplicate_status == '3')
                                                        <small>ไม่อยู่ในสถานะเบิกจ่าย</small>
                                                    @elseif($TranferDropCenterDuplicate->duplicate_status == '4')
                                                        <small>ไม่พบรายการพัสดุนี้</small>
                                                    @endif
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div align='center' style="padding-top:20px;">Empty...</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        
    });
    $("#submitsavetransfer").click(function(){
        $("#submitsavetranferform").trigger("click");
    });

    $("#duplicates_tap").click(function(){
        duplicates_body = $("#duplicates_body").is(":hidden");
        if(duplicates_body == false){
            $("#duplicates_body").hide();
        }else{
            $("#duplicates_body").show();
        }
    });
    
    $("#submitsavetransfer").click(function(){
        $("#submitsavetranferform").trigger( "click" );
        if($("#tranfer_driver_sender_name").val() !== '' && $("#tranfer_driver_sender_numberplate").val() !== '' && $("#tranfer_driver_sender_phone").val() !== ''){
            $("#submitsavetransfer").attr('Disabled', true);
        }
    });

    function check_input_submit(){
        if($("#tranfer_driver_sender_name").val() !== '' && $("#tranfer_driver_sender_numberplate").val() !== '' && $("#tranfer_driver_sender_phone").val() !== ''){
            $("#submitsavetransfer").attr('Disabled', false);
        }else{
            $("#submitsavetransfer").attr('Disabled', true);
        }
    }

    <?php
        $Duplicates_Qty = count($TranferDropCenterDuplicates);
        if($Duplicates_Qty == Session::get('Duplicates_Qty') && Session::get('Duplicates_Name') == "tranfer_to_dc"){
    ?>
        $("#duplicates_body").hide();
    <?php
        }
        Session::put('Duplicates_Qty', $Duplicates_Qty);
        Session::put('Duplicates_Name', "tranfer_to_dc");
    ?>
</script>
@endsection

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-white bg-success">
          <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            @if (count($TransferDropCenterBills) > 0)
                @php
                    $i = 0;
                @endphp
                <table width='100%'>
                    <thead>
                        <tr>
                            <th>ที่</th>
                            <th>รหัส</th>
                            <th>เวลา</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                
                        @foreach ($TransferDropCenterBills as $TransferDropCenterBill)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$TransferDropCenterBill->transfer_bill_no}}</td>
                                <td>{{$TransferDropCenterBill->created_at}}</td>
                                <td align="center"><a href="/linehallDetail/{{$TransferDropCenterBill->id}}" target='_blank'><i class="fa fa-print" aria-hidden="true"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div align='center' style='color:red;'>ยังไม่มีรายการส่งพัสดุให้สาขาปลายทา</div>
            @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">ปิด</button>
        </div>
      </div>
    </div>
</div>
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> --}}
