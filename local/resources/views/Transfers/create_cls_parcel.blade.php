@extends("welcome")
@section("content")



<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">
            <span>Parcel CLS</span> 
            <span class="form-inline" style='margin-left:15px;'>
                <select class="custom-select mr-sm-2" id="ListType" onchange="getFilter(this)">
                    <option selected value="0">ทั้งหมด</option>
                    <option value="1">จากเขตอำเภอ</option>
                    <option value="2">จากเขตตำบล</option>
                    <option value="4">รอส่งให้ปลายทาง</option>
                    <option value="3">รายการส่งสำเร็จ</option>
                </select>
            </span>
            <span class="form-inline" style='margin-left:15px;'>
                <select class="custom-select mr-sm-2" id="Amphure" onchange="getFilterAmpure(this)">
                    <option selected>เลือกอำเภอ</option>
                    @foreach ($amphure as $amphur)
                        <option value="{{ $amphur->id }}">{{ $amphur->name_th }}</option>
                    @endforeach
                </select>
            </span>
            <span class="form-inline" style='margin-left:15px;'>
                <select class="custom-select mr-sm-2" id="Distric" onchange="getFilterDistric(this)">
                    <option selected>เลือกตำบล</option>
                    @foreach ($districs as $distric)
                        <option value="{{ $distric->id }}">{{ $distric->name_th }}</option>
                    @endforeach
                </select>
            </span>
            {{-- <span class="form-inline" style='margin-left:15px; position: absolute; right:15px;'>
                <select class="custom-select mr-sm-2" id="Distric" onchange="wherebystatus(this)">
                    <option selected>แสดงจากสถานะ</option>
                    <option value="AND tracking_statusnow = 'done'">รายการรับเข้าหน้าร้าน</option>
                    <option value="AND tracking_statusnow = 'ReceiveDone'">รายการรับเข้าจากต้นทาง</option>
                </select>
            </span> --}}
        </div>
        <div class="card-body table-responsive">
            <div id="tablelist">
            </div>
        </div>
        {{-- <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%" class="text-center">Tracking No</th>
                        <th width="20%" class="text-left">ชื่อผู้ส่ง</th>
                        <th width="20%" class="text-left">ชื่อผู้รับ</th>
                        <th width="15%" class="text-center"> สถานะ</th>
                        <th width="10%" class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($trackings))
                    @php
                        $i=1;
                    @endphp
                        @foreach ($trackings as $tracking)
                        <tr>
                            <td class="text-center ">{{$i++}}
                                <input type="hidden" name="_trackingId" id="trackingId" value="{{$tracking->tracking_id}}">
                                <input type="hidden" name="_trackingId" id="trackingId" value="{{$tracking->id}}">
                            </td>
                            <td class="text-center ">{{$tracking->tracking_no}}</td>
                            <td class="text-left">
                                @php
                                    $sender_name = '';
                                    $sender = App\Model\Booking::where('id',$tracking->tracking_booking_id)->first();
                                    if($sender){
                                        $sender_name = $sender->customer->cust_name;
                                    }
                                @endphp
                                {{$sender_name}}
                            </td>
                            <td class="text-left">{{$tracking->receiver->cust_name}}</td>
                            <td class="text-center">
                                {{$tracking->tracking_status}}
                               @if ($tracking->tracking_status == 'done')
                                   อยู่ที่ DC
                               @endif
                            </td>
                            <td class="text-center">
                                <button class="mb-2 mr-2 border-0 btn-transition btn btn-outline-danger" onClick="addStatusWrong();">ทำรายการ
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table> <br><br><br><br>
            <div class="d-block text-left card-footer">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <a href="/saveStatusDoneToTransferBill/{{!empty($parcelBillId) ? $parcelBillId : null}}"><button class="btn-wide  btn btn-primary" type="submit" >บันทึกข้อมูล</button></a>
                        <a href="/getParcelListFromOtherDC/1"><button class="btn-wide  btn btn-secondary">กลับ</button></a>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token()}}">
</div>
<script>
    function addStatusWrong(trackingId){
        // var token = $('#token').val();
        Swal.fire({
            type: 'warning',
            title: 'แจ้งส่งกลับพัสดุติดปัญหา',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<form action="/create_parcel_wrong" method="POST">'+
                    '<input type="hidden" name="_token" value="{{ csrf_token() }}">'+
                    '<input type="hidden" name="tracking_id" value="'+trackingId+'">'+
                    '<div class="row"><br><br>'+
                        '<div class="col-lg-12 col-md-12 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="province" class="">ระบุเหตุผลที่ติดปัญหา</label>'+
                                '<textarea name="wrong_problem_detail" id="exampleText" class="form-control" row="5"></textarea>'+
                                '<div class="row" id="request_currier_id" style="display:block;">'+
                                    '<div class="col-lg-12 col-md-12">'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+
                        '<div class="col-lg-12 col-md-12">'+
                            '<button type="submit" class="mt-1 btn btn-danger">แจ้งสถานะรอส่งกลับ</button>'+
                        '</div>'+
                    '</div>'+
                    '</form>'
        });
    }
    
    function viewDetail(cdName, booking_no, tracking_no, parcelamount, created_at, orther_dc_revice_time, sender_id, recive_id){
        if(orther_dc_revice_time == '// '){
            // alert('ss');
            orther_dc_revice_time = created_at;
        }
        // alert(orther_dc_revice_time);
        $.ajax({
            method:"POST",
            url:"{{url('findsender_revice')}}",
            dataType: 'json',
            data:{"sender_id":sender_id, "recive_id":recive_id, "_token": "{{ csrf_token() }}"},
            success:function(data){

                sender = data[0][0].cust_name+'<br>';
                sender += data[0][0].cust_address+' ';
                sender += data[0][0].district.name_th+' ';
                sender += data[0][0].amphure.name_th+' ';
                sender += data[0][0].province.name_th+' ';
                sender += data[0][0].cust_postcode+' ';
                sender += data[0][0].cust_phone;

                recive = data[1][0].cust_name+'<br>';
                recive += data[1][0].cust_address+' ';
                recive += data[1][0].district.name_th+' ';
                recive += data[1][0].amphure.name_th+' ';
                recive += data[1][0].province.name_th+' ';
                recive += data[1][0].cust_postcode+' ';
                recive += data[1][0].cust_phone;

                Swal.fire({
                    showCancelButton: false,
                    showConfirmButton: false,
                    reverseButtons: false,
                    html:   '<div class="row">'+
                                '<div class="col-lg-12 col-md-12" style="color:blue;">'+tracking_no+'</div>'+
                                '<div class="col-lg-12 col-md-12" align="left" style="font-size:14px;"><b>รับโดยสาขา : </b>'+cdName+'</div>'+
                                '<div class="col-lg-12 col-md-12" align="left" style="font-size:14px;"><b>สร้างรายการเมื่อ : </b>'+created_at+'</div>'+
                                '<div class="col-lg-12 col-md-12" align="left" style="font-size:14px;"><b>จำนวน : </b>'+parcelamount+' (ชิ้น/กล่อง)</div>'+
                                '<div class="col-lg-12 col-md-12" align="left" style="font-size:14px;"><b>เข้าคลังสาขาเมื่อ : </b>'+orther_dc_revice_time+'</div>'+
                                '<div class="col-lg-12 col-md-12" align="left" style="font-size:14px;"><br><b>ผู้ส่ง : </b>'+sender+'</div>'+
                                '<div class="col-lg-12 col-md-12" align="left" style="font-size:14px;"><br><b>ผู้รับ : </b>'+recive+'</div>'+
                            '</div>'
                });
            }
        });
    }

    var ListType = 0;
    var ap = 0;
    var dt = 0;
    var whereby = "";
    $(document).ready(function(){
        // ListType = 0;
        parcel_in_dropcenter();
        $("#Amphure").css('display','none');
        $("#Distric").css('display','none');
    });
    function getFilter(type){
        if(type.value == '0'){
            ListType = 0;
            parcel_in_dropcenter();
            $("#Amphure").css('display','none');
            $("#Distric").css('display','none');
        }else if(type.value == '1'){
            $("#Amphure").css('display','block');
            $("#Distric").css('display','none');
        }else if(type.value == '2'){
            $("#Amphure").css('display','none');
            $("#Distric").css('display','block');
        }else if(type.value == '3'){
            ListType = 3;
            parcel_in_dropcenter();
            $("#Amphure").css('display','none');
            $("#Distric").css('display','none');
        }else if(type.value == '4'){
            ListType = 4;
            parcel_in_dropcenter();
            $("#Amphure").css('display','none');
            $("#Distric").css('display','none');
        }
    }
    function parcel_in_dropcenter(){
        content = "<table class='table data-table' >";
            content += "<thead>";
                content += "<tr>";
                    content += "<th>No</th>";
                    content += "<th>หมายเลขพัสดุ</th>";
                    content += "<th>ชื่อผู้ส่ง</th>";
                    content += "<th>ชื่อผู้รับ</th>";
                    content += "<th>COD</th>";
                    content += "<th>ประเภทรับงาน</th>";
                    content += "<th>สถานะงาน</th>";
                    content += "<th>เวลารับเข้าสาขา</th>";
                    content += "<th width='125px'>ทำรายการ</th>";
                content += "</tr>";
            content += "</thead>";
            content += "<tbody>";
            content += "</tbody>";
        content += "</table>";
        $("#tablelist").html(content);
        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    method:"POST",
                    url:"{{url('cls_tracking_listFilter')}}",
                    dataType: 'json',
                    data:{
                            "_token": "{{ csrf_token() }}",
                            "id": "{{ $id }}",
                            "ListType": ListType,
                            "ap": ap,
                            "dt": dt,
                            "whereby": whereby
                        },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'tracking_no', name: 'tracking_no'},
                    {data: 'cust_send_name', name: 'cust_send_name'},
                    {data: 'cust_name', name: 'cust_name'},
                    {data: 'subtracking_cod', name: 'subtracking_cod', className:'text-right'},
                    {data: 'booking_type', name: 'booking_type'},
                    {data: 'tracking_status', name: 'tracking_status'},
                    {data: 'orther_dc_revice_time', name: 'orther_dc_revice_time'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
        // table = $('.data-table').DataTable( {
        //     paging: false
        // } );
        // alert(table);
    }

    function getFilterAmpure(Ampuure){
        ListType = $("#ListType").val();
        ap = Ampuure.value;
        dt = 0;
        parcel_in_dropcenter();
    }
    
    function getFilterDistric(Distric){
        ListType = $("#ListType").val();
        ap = 0;
        dt = Distric.value;
        parcel_in_dropcenter();        
    }
    
    function wherebystatus(whereby){
        ListType = $("#ListType").val();
        whereby = whereby.value;
        parcel_in_dropcenter();        
    }

    function CancelStatusWrong(trackingId){
        Swal.fire({
        title: 'ยกเลิกรายการส่งกลับพัสดุ ?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ฉันต้องการยกเลิก'
        }).then((result) => {
            if (result.value) {
                window.location="/Cancel_StatusWrong/"+trackingId;
            }
        });
    }

</script>

@endsection
