@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">ปิดงาน จ่ายลูกค้าปลายทาง / คืนผู้ส่ง</div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-1 col-md-1 text-right" style="font-size:13px;">Tracking No</div>
                <div class="col-lg-3 col-md-3">
                    <div class="position-relative form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" name="tracking_no" id="tracking_no" autofocus>
                            <div class="input-group-append">
                                <button class="btn btn-primary" id="submit_form" onclick="get_detail()">ค้นหา</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    @if (!empty($Tracking))
                        <button class="btn btn-success pull-right" onclick="podClos('{{ $Tracking->id }}','{{ $Tracking->tracking_no }}')">ยืนยันจ่ายพัสดุ/ปิดPOD</button>
                    @endif
                </div>
            </div>
        </div>
        @if (!empty($Tracking))
            <div class="card-body" id="track_detailfrom">
                <div class="row">
                    <div class="col-md-12 col-lg-6">
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <div class="card-title">ผู้ส่ง</div>
                                <div class="table-responsive">
                                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                        <thead>
                                            <tr>  
                                                <th width="30%" class="text-left"></th>
                                                <th width="70%" class="text-left"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>  
                                                <td class="text-left">ชื่อ :</td>
                                                <td class="text-left">{{ $Tracking->booking->customer->cust_name }}</td>
                                            </tr>  
                                            <tr>  
                                                <td class="text-left">ที่อยู่ :</td>
                                                <td class="text-left">{{ $Tracking->booking->customer->cust_address.' '.$Tracking->booking->customer->District->name_th.' '.$Tracking->booking->customer->amphure->name_th.' '.$Tracking->booking->customer->province->name_th.' '.$Tracking->booking->customer->cust_postcode }}</td>
                                            </tr>  
                                            <tr>  
                                                <td class="text-left">เบอร์มือถือ :</td>
                                                <td class="text-left">{{ $Tracking->booking->customer->cust_phone }}</td>
                                            </tr>  
                                            <tr>  
                                                <td class="text-left">สาขาต้นทาง :</td>
                                                <td class="text-left">{{ $Tracking->booking->DropCenter->drop_center_name_initial }}</td>
                                            </tr> 
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <div class="card-title">ผู้รับ</div>
                                <div class="table-responsive">
                                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                        <thead>
                                            <tr>  
                                                <th width="30%" class="text-left"></th>
                                                <th width="70%" class="text-left"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>  
                                                <td class="text-left">ชื่อ :</td>
                                                <td class="text-left">{{ $Tracking->customer->cust_name }}</td>
                                            </tr>  
                                            <tr>  
                                                <td class="text-left">ที่อยู่ :</td>
                                                <td class="text-left">{{ $Tracking->customer->cust_address.' '.$Tracking->customer->District->name_th.' '.$Tracking->customer->amphure->name_th.' '.$Tracking->customer->province->name_th.' '.$Tracking->customer->cust_postcode }}</td>
                                            </tr>  
                                            <tr>  
                                                <td class="text-left">เบอร์มือถือ :</td>
                                                <td class="text-left">{{ $Tracking->customer->cust_phone }}</td>
                                            </tr>  
                                            <tr>  
                                                <td class="text-left">สาขาต้นทาง :</td>
                                                <td class="text-left">{{ $Tracking->customer->PostCode->DropCenter->drop_center_name_initial }}</td>
                                            </tr> 
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">  
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                        <thead>
                            <tr>  
                                <th width="10%" class="text-center">Tracking No</th>
                                <th width="10%" class="text-center">SubTracking No</th>
                                <th width="10%" class="text-center">Parcel Type </th>
                                <th width="20%" class="text-center">Parcel Dimension Detail </th>
                                <th width="10%" class="text-right">Parcel Weight</th>
                                <th width="10%" class="text-right">COD</th>
                                <th width="20%" class="text-right">SubTracking Amount/Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $cost = 0;
                            @endphp
                            @if (count($SubTrackings) > 0)
                                @foreach ($SubTrackings as $SubTracking)
                                    @php
                                        if (strpos($Tracking->tracking_status, 'Return') === FALSE){
                                            $title = "ยอดค่าบริการเก็บเงินปลายทาง";
                                            $cost += $SubTracking->subtracking_cod;
                                        }else{
                                            $title = "ยอดค่าบริการการตีกลับพัสดุ";
                                            $cost += $SubTracking->subtracking_price;
                                        }
                                    @endphp
                                    <tr>  
                                        <td class="text-center">{{ $SubTracking->tracking->tracking_no }}</td>
                                        <td class="text-center">{{ $SubTracking->subtracking_under_tracking_id }}</td>
                                        <td class="text-center">
                                            @if ($SubTracking->subtracking_dimension_type == '1')
                                                เลือกจากกล่อง
                                            @else
                                                กำหนดเอง
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $SubTracking->DimensionHistory->dimension_history_width }}x{{ $SubTracking->DimensionHistory->dimension_history_length }}x{{ $SubTracking->DimensionHistory->dimension_history_hight }}</td>
                                        <td class="text-right">{{ number_format($SubTracking->DimensionHistory->dimension_history_weigth,0) }}</td>
                                        <td class="text-right">{{ number_format($SubTracking->subtracking_cod,2) }}</td>
                                        <td class="text-right">{{ number_format($SubTracking->subtracking_price,2) }}</td>
                                    </tr> 
                                @endforeach  
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    var input = document.getElementById("tracking_no");
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            get_detail();
        }
    });

    function get_detail(){
        track_id = input.value;
        window.location = "/Tranfer_pod_closing/{{$employee->emp_branch_id}}/"+track_id;
    }
    @if (!empty($Tracking))
        function podClos(tracking_id, tracking_no){
            Swal.fire({
                showCancelButton: false,
                showConfirmButton: false,
                reverseButtons: false,
                html:   '<div class="row" style="margin-left: -58px !important; margin-right: -58px !important; margin-top: -15px !important;">'+
                            '<div class="col-lg-12 col-md-12">'+
                                '<div class="card">'+
                                    '<div class="card-header bg-success" style="color:#fff;">ปิด POD : '+tracking_no+'</div>'+
                                    '<div class="card-body text-left" style="font-size:14px;">'+
                                        '<form action="/pod_closing_form_submit" method="POST">'+
                                            '<input type="hidden" name="_token" value="{{ csrf_token() }}">'+
                                            '<input type="hidden" name="transfer_booking_id" value="{{ $Tracking->booking->id }}">'+
                                            '<input type="hidden" name="transfer_status" value="CustomerResiveDone">'+
                                            '<input type="hidden" name="transfer_tracking_id" value="'+tracking_id+'">'+
                                            '<input type="hidden" name="parcel_amount" value="{{count($SubTrackings)}}">'+
                                            '<input type="hidden" name="photo" id= "photo" value="">'+
                                            '<div align="center">'+
                                                '<div id="my_camera" style="width:240; height:240; border:1px solid #000;" align="center">ถ่ายภาพ</div>'+
                                            '</div>'+
                                            '<div class="row">'+
                                                '<div class="col-lg-12 col-md-12" align="center"><br>'+
                                                    '<a href="javascript:void(take_snapshot())" id="takephoto"><button type="button" class="btn btn-warning">ถ่ายภาพ</button></a> '+
                                                    '<button type="button" onclick="call_cam();" class="btn btn-info">เปิดกล้อง/ถ่ายใหม่</button> '+
                                                    '<button type="button" onclick="closing_cam()" class="btn btn-secondary">ปิดกล้อง</button>'+
                                                '</div>'+
                                                '<div class="col-lg-6 col-md-6"><br>'+
                                                    '<label>ชื่อผู้รับ</label>'+
                                                    '<input type="text" name="receive_name" value="" class="form-control" placeholder="ชื่อผู้รับ" required>'+
                                                '</div>'+
                                                '<div class="col-lg-6 col-md-6"><br>'+
                                                    '<label>ความสัมพันธ์</label>'+
                                                    '<select name="receive_relation" onchange="recive_value(this)" class="form-control" required>'+
                                                        '<option value=""> --กรุณาเลือก-- </option>'+
                                                        '<option value="ผู้รับรับเอง">ผู้รับรับเอง</option>'+
                                                        '<option value="ผู้ส่งรับเอง">ผู้ส่งรับเอง</option>'+
                                                        '<option value="ญาติ">ญาติ</option>'+
                                                        '<option value="รปภ">รปภ</option>'+
                                                        '<option value="0">อื่นๆ</option>'+
                                                    '</select>'+
                                                    '<input type="text" name="receive_relation_orther" id="receive_relation_orther" value="" class="form-control" placeholder="โปรดความสัมพันธ์">'+
                                                '<br></div>'+
                                                '<div class="col-lg-12 col-md-12 bg-danger" align="center">'+
                                                    '<span style="font-size:14px; color:#fff;">{{ $title }}</span><br>'+
                                                    '<span style="font-size:30px; color:#fff;">{{ number_format($cost,2) }}</span><span style="font-size:14px; color:#fff;">บาท</span>'+
                                                '</div>'+
                                                '<div class="col-lg-12 col-md-12"><br>'+
                                                    @if ($cost > 0)
                                                        '<button type="submit" class="btn btn-success btn-lg pull-right" onclick="confirm(\'ยืนยันการรับยอดปิดPOD {{ number_format($cost,2) }}บาท\')">ปิด POD</button>'+
                                                    @else
                                                        '<button type="submit" class="btn btn-success btn-lg pull-right" onclick="confirm(\'ยืนยันการปิดPOD\')">ปิด POD</button>'+
                                                    @endif
                                                    @if(strpos($Tracking->tracking_status, 'Return') === FALSE)
                                                        '<input type="hidden" value="1" name="close_type" />'+
                                                        '<input type="hidden" value="{{ number_format($cost,0) }}" name="money_amount" />'+
                                                    @else
                                                        '<input type="hidden" value="2" name="close_type" />'+
                                                        '<input type="hidden" value="{{ number_format($cost,0) }}" name="money_amount" />'+
                                                    @endif

                                                '</div>'+
                                            '</div>'+
                                        '</form>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'
            });
            // call_cam();
            $("#takephoto").hide();
            $("#receive_relation_orther").attr("hidden",true);
            $("#receive_relation_orther").attr("required",false);
        }
    @endif
    function call_cam(){
        Webcam.reset();
        Webcam.attach( '#my_camera' );
        $("#takephoto").show();
        $("#photo").val("");
    }

    function closing_cam(){
        Webcam.reset();
        $("#my_camera").html("ถ่ายภาพ");
        $("#takephoto").hide();
        $("#photo").val("");
    }

    function recive_value(select){
        if(select.value == '0'){
            $("#receive_relation_orther").attr("hidden",false);
            $("#receive_relation_orther").attr("required",true);
        }else{
            $("#receive_relation_orther").attr("hidden",true);
            $("#receive_relation_orther").attr("required",false);
        }
    }

    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            document.getElementById('my_camera').innerHTML = '<img src="'+data_uri+'"/>';
            $("#photo").val(data_uri);
        } );
    }

</script>

@endsection
