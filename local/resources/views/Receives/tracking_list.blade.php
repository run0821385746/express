@extends("welcome")
@section("content")
<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">
            <span>รายการพัสดุเลื่อนรับ และติดปัญหา</span> 
            <span class="form-inline" style='margin-left:15px;'>
                <select class="custom-select mr-sm-2" id="ListType" onchange="getFilter(this)">
                    <option selected value="0">ทั้งหมด</option>
                    <option value="1">รายการเลื่อนรับ</option>
                    <option value="2">รายการติดปัญหา</option>
                    <option value="3">รายการค้างคลังเกิน4วัน</option>
                    {{-- <option value="3">รายการส่งสำเร็จ</option> --}}
                </select>
            </span>
            {{-- <span class="form-inline" style='margin-left:15px;'>
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
            </span> --}}
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
            {{-- <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>  
                        <th width="5%" class="text-center">No</th>
                        <th width="10%" class="text-center">Booking No</th>
                        <th width="10%" class="text-center">Tracking No</th>
                        <th width="15%" class="text-left"> ข้อมูลผู้ส่ง</th>
                        <th width="10%" class="text-right"> COD</th>
                        <th width="15%" class="text-center"> ประเภทรับงาน</th>
                        <th width="10%" class="text-center">สถานะพัสดุ</th>
                        <th width="15%" class="text-center">เวลาทำรายการ</th>
                        <th width="5%" class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @if (!empty($trackingList))
                        @foreach ($trackingList as $tracking)
                            <tr>  
                                <td class="text-center text-muted">{{$i++}}</td>
                                <td class="text-center text-muted">{{$tracking->booking_no}}</td>
                                <td class="text-center text-muted">{{$tracking->tracking_no}}</td>
                                <td class="text-left">
                                    @php
                                        $receiver = App\Model\Customer::where('id',$tracking->tracking_receiver_id)->first();
                                    @endphp
                                    @if (!empty($receiver->cust_name))
                                        {{$receiver->cust_name}}
                                    @endif
                                </td>
                                <td class="text-right">
                                    @php
                                        $subTrackings = App\Model\SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                                        $count_cod = 0;
                                        if($subTrackings){
                                            foreach ($subTrackings as $subTracking) {
                                                $count_cod += $subTracking->subtracking_cod;
                                            }
                                        }
                                    @endphp
                                    {{number_format($count_cod,2)}}
                                </td>
                                <td class="text-center">
                                    @if ($tracking->booking_type=='1')
                                    พัสดุรับหน้าร้าน
                                    @else
                                    เรียกรถเข้ารับพัสดุ
                                    @endif
                                </td>
                                <td class="text-center">{{$tracking->tracking_status}}</td>
                                <td class="text-center">{{$tracking->created_at}}</td>
                                <td class="text-center">
                                    <a href="/previewTrackingBarcode/{{$tracking->id}}" target="blank">
                                        <button type="submit" id="PopoverCustomT-1" class="btn btn-primary btn-sm"><i
                                            class="metismenu-icon pe-7s-note2"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>  
                        @endforeach  
                    @endif
                </tbody>
            </table><br><br> --}}
    </div>
</div>

@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    var ListType = 0;
    // var ap = 0;
    // var dt = 0;
    // var whereby = "";
    $(document).ready(function(){
        parcel_in_dropcenter();
    });
    function getFilter(type){
        if(type.value == '0'){
            ListType = 0;
            parcel_in_dropcenter();
        }else if(type.value == '1'){
            ListType = 1;
            parcel_in_dropcenter();
        }else if(type.value == '2'){
            ListType = 2;
            parcel_in_dropcenter();
        }else if(type.value == '3'){
            ListType = 3;
            parcel_in_dropcenter();
        }
    }

    function parcel_in_dropcenter(){
        content = "<table class='table data-table' >";
            content += "<thead>";
                content += "<tr>";
                    content += "<th>No</th>";
                    content += "<th>หมายเลขพัสดุ</th>";
                    content += "<th>ชื่อผู้รับ</th>";
                    content += "<th>ประเภทรับงาน</th>";
                    content += "<th>สถานะงาน</th>";
                    content += "<th>การนำส่ง</th>";
                    content += "<th>อยู่ที่สาขา</th>";
                    content += "<th>ปรับปรุงล่าสุด</th>";
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
                    url:"{{url('tracking_listFilter')}}",
                    dataType: 'json',
                    data:{
                            "_token": "{{ csrf_token() }}",
                            "id": "{{ $id }}",
                            "ListType": ListType
                        },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'tracking_no', name: 'tracking_no'},
                    {data: 'cust_name', name: 'cust_name'},
                    {data: 'booking_type', name: 'booking_type'},
                    {data: 'tracking_status', name: 'tracking_status'},
                    {data: 'sendcount', name: 'sendcount', className:"text-center"},
                    {data: 'inDcdate', name: 'inDcdate', className:"text-right"},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
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

    function findsendHistory(id){
        // alert(trackId);
        $.ajax({
            method:"POST",
            url:"{{url('find_sendHistory')}}",
            dataType: 'json',
            data:{"id":id,"_token": "{{ csrf_token() }}"},
            success:function(data){
                // console.log(data);
                result = JSON.parse(data);
                content = '';
                $.each(result, function(i, item){
                    date = item.date.substr(8, 2)+'/';
                    date += item.date.substr(5, 2)+'/';
                    date += item.date.substr(0, 4)+' ';
                    date += item.date.substr(11, 5);

                    callhistory = item.call;
                    // alert(callhistory.length);
                    content += '<div class="col-lg-12 col-md-12" align="left" style="font-size:14px;"><b>รับโดยสาขา : </b>'+item.courier+'</div>';
                    content += '<div class="col-lg-12 col-md-12" align="left" style="font-size:14px;"><b>เวลานำจ่าย : </b>'+date+'</div>';
                    content += '<div class="col-lg-12 col-md-12" align="left" style="font-size:14px; padding-left:30px;">';
                        content += '<table width="100%">';
                        content += '<tr style="background-color:#ddd;">';
                            content += '<td>No</td>';
                            content += '<td>Note</td>';
                            content += '<td>OnCall</td>';
                            content += '<td>OnTalk</td>';
                            content += '<td>CallTime</td>';
                        content += '</tr>';
                        if(callhistory.length > 0){
                            $.each(callhistory, function(ii, iitem){

                                iidate = iitem.callTime.substr(8, 2)+'/';
                                iidate += iitem.callTime.substr(5, 2)+'/';
                                iidate += iitem.callTime.substr(0, 4)+' ';
                                iidate += iitem.callTime.substr(11, 5);
                                if(iitem.callstatus == 1){
                                    pick_time = iitem.pick_time.substr(8, 2)+'/';
                                    pick_time += iitem.pick_time.substr(5, 2)+'/';
                                    pick_time += iitem.pick_time.substr(0, 4)+' ';
                                    pick_time += iitem.pick_time.substr(11, 5);
                                    content += '<tr>';
                                        content += '<td>'+(ii+1)+'</td>';
                                        content += '<td align="left">'+iitem.note+'</td>';
                                        content += '<td align="center">'+iitem.oncall+'</td>';
                                        content += '<td align="center">'+iitem.ontalk+'</td>';
                                        content += '<td>'+iidate+'</td>';
                                    content += '</tr>';
                                    content += '<tr>';
                                        content += '<td></td>';
                                        content += '<td colspan="4">'+pick_time+'</td>';
                                    content += '</tr>';
                                }else{
                                    content += '<tr>';
                                        content += '<td>'+(ii+1)+'</td>';
                                        content += '<td align="left">'+iitem.note+'</td>';
                                        content += '<td align="center">'+iitem.oncall+'</td>';
                                        content += '<td align="center">'+iitem.ontalk+'</td>';
                                        content += '<td>'+iidate+'</td>';
                                    content += '</tr>';
                                }
                            });
                        }else{
                            content += '<tr>';
                                content += '<td colspan="5" align="center">ยังไม่มีการติดต่อ</td>';
                            content += '</tr>';
                        }
                        content += '</table>';
                    content += '</div>';
                });

                Swal.fire({
                    showCancelButton: false,
                    showConfirmButton: false,
                    reverseButtons: false,
                    html:   '<div class="row">'+
                            '<div class="col-lg-12 col-md-12" style="color:blue;">ประวัติการนำส่ง</div>'+
                            content+
                            '</div>'
                });
            }
        });
    }

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