@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">เรียกคืนพัสดุ</div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-1 col-md-1 text-right" style="font-size:13px;"><br>Tracking No</div>
                <div class="col-lg-3 col-md-3"> <br>
                    <div class="position-relative form-group">
                        <form action="/return_parcel" method="POST">
                            {{csrf_field()}}
                            @method('PUT')
                            <div class="input-group">
                                {{-- <input type="hidden" class="form-control" name="transfer_bill_id" value="{{!empty($TranserBill->id) ? $TranserBill->id : null}}"> --}}
                                <input type="text" class="form-control" name="tracking_no" autofocus>
                                <input type="hidden" name="branch_id" value="{{$id}}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary">ค้นหา</button>
                                </div>
                            </div>
                        </form>   
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    @if(!empty($ReturnParcels))
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th class="text-center">Tracking No</th>
                            <th class="text-center">จำนวนพัสดุ</th>
                            <th class="text-center">สถานะเรียกคืน</th>
                            <th width="10%" class="text-center">ทำรายการ</th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @php
                        $btn = 0;
                    @endphp
                    @if(count($ReturnParcels) > 0)
                        @php
                            $i = 1;
                            $btn = 1;
                        @endphp
                        @foreach ($ReturnParcels as $ReturnParcel)
                            @php
                                $parcel_receive = explode(",", $ReturnParcel->parcel_receive);
                                sort($parcel_receive);
                            @endphp
                            @for ($iforbox = 0; $iforbox < count($parcel_receive); $iforbox++)
                                @if ($iforbox == 0)
                                    <tr>
                                        <td class="text-center text-muted">{{$i++}}</td>
                                        <td class="text-center">
                                            @php
                                                $rtnshow = "";
                                                if(strpos($ReturnParcel->return_status, 'Return') !== false){
                                                    $rtnshow = "(RTN)";
                                                }
                                            @endphp
                                            {{$ReturnParcel->Tracking->tracking_no.$rtnshow}}          
                                        </td>
                                        <td class="text-center">
                                            @if ($ReturnParcel->parcel_receive == null)
                                                0/{{$ReturnParcel->parcel_amount}}
                                            @else
                                                {{$parcel_receive[$iforbox].'/'.$ReturnParcel->parcel_amount}}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (count($parcel_receive) == $ReturnParcel->parcel_amount)
                                                <p style="color: green;">{{$ReturnParcel->return_status}}</p>
                                            @else
                                                @php
                                                    $btn = 0;
                                                @endphp
                                                {{$ReturnParcel->return_status}}
                                            @endif
                                        </td>
                                        <td class="text-center" style="padding: 0px;" rowspan="{{count($parcel_receive)}}">
                                            <a href="/return_Parcel_delete/{{$ReturnParcel->id}}" class="border-0 btn-transition btn btn-outline-danger">ลบ</a>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="text-center text-muted">{{$i++}}</td>
                                        <td class="text-center">
                                            @php
                                                $rtnshow = "";
                                                if(strpos($ReturnParcel->return_status, 'Return') !== false){
                                                    $rtnshow = "(RTN)";
                                                }
                                            @endphp
                                            {{$ReturnParcel->Tracking->tracking_no.$rtnshow}}          
                                        </td>
                                        <td class="text-center">
                                            @if ($ReturnParcel->parcel_receive == null)
                                                0/{{$ReturnParcel->parcel_amount}}
                                            @else
                                                {{$parcel_receive[$iforbox].'/'.$ReturnParcel->parcel_amount}}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (count($parcel_receive) == $ReturnParcel->parcel_amount)
                                                <p style="color: green;">{{$ReturnParcel->return_status}}</p>
                                            @else
                                                @php
                                                    $btn = 0;
                                                @endphp
                                                {{$ReturnParcel->return_status}}
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endfor
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center "><font color="red">ไม่พบรายการเรียกคืน</font></td>
                        </tr>
                    @endif

                    {{-- <div style="position:fixed; bottom:0px; right:10px; width:300px; z-index:1;" class="card">
                        <div class="card-header bg-primary" id="duplicates_tap">
                            รายการแสกนซ้ำ
                        </div>
                        <div class="card-body" style="padding:0px; height:200px; overflow:scroll;" id="duplicates_body">
                            @if (count($TransfersDuplicates) > 0)
                                <ul class="list-group">
                                    @foreach ($TransfersDuplicates as $TransfersDuplicate)
                                        <li class="list-group-item">
                                            <b style="color:red;">{{$TransfersDuplicate->duplicate_tracking_no}}</b>
                                            <br>
                                            <small>
                                                {{date_format($TransfersDuplicate->created_at,"d/m/Y H:i:s")}}
                                            </small>
                                            <span class="pull-right">
                                                @if ($TransfersDuplicate->duplicate_status == '1')
                                                    <small>ไม่พบรายการ Tracking</small>
                                                @elseif ($TransfersDuplicate->duplicate_status == '2')
                                                    <small>ทำจ่ายซ้ำ</small>
                                                @elseif ($TransfersDuplicate->duplicate_status == '3')
                                                    <small>รายการนี้เบิกไปจ่ายแล้ว</small>
                                                @elseif ($TransfersDuplicate->duplicate_status == '4')
                                                    <small>ปลายทางรับแล้ว</small>
                                                @elseif ($TransfersDuplicate->duplicate_status == '5')
                                                    <small>ไม่อยู่ในสถานะทำเบิกจ่าย</small>
                                                @elseif ($TransfersDuplicate->duplicate_status == '6')
                                                    <small>พื้นที่COURIER หรือปลายทางไม่ถูกต้อง</small>
                                                @else
                                                    <small>Tracking ไม่อยู่ในรายการจัดส่ง</small>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div align='center' style="padding-top:20px;">Empty...</div>
                            @endif
                        </div>
                    </div> --}}
                </tbody>
            </table>
        </div>
        <div class="card-body">
            <div>
                @if ($btn == '1')
                    <a href="/save_return_back_to_dc/{{$employee->emp_branch_id}}"><button class="btn btn-primary pull-right">เรียกคืน</button></a> 
                @else
                    <button class="btn btn-primary pull-right" disabled>เรียกคืน</button>
                    {{-- <a href="/save_return_back_to_dc/{{$employee->emp_branch_id}}"><button class="btn btn-primary pull-right">เรียกคืน</button></a>  --}}
                @endif
            </div>
        </div>
    </div>
</div>

{{-- <div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">
            <span>Sending Tracking List</span> 
        </div>
        <div class="card-body table-responsive">
            <div id="tablelist">
            </div>
        </div>
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token()}}">
</div> --}}
<script>
    // tranfer_sending();
    function viewDetail(id, track_id, courier_id, color, contenttext){
        // alert(color);
        if(color == "outline-secondary"){
            color = "secondary";
        }else if(color == "outline-primary"){
            color = "primary";
        }else if(color == "outline-danger"){
            color = "danger";
        }
        $.ajax({
            method:"POST",
            url:"{{url('getfind_call_history')}}",
            dataType: 'json',
            data:{"id":id, "track_id":track_id, "courier_id":courier_id, "_token": "{{ csrf_token() }}"},
            success:function(data){
                content_list = '<table class="table table-bordered">';
                    content_list += '<tr>';
                        content_list += '<th>No</th>';
                        content_list += '<th>เหตุผล</th>';
                        content_list += '<th>ระยะเวลาโทร/วินาที</th>';
                        content_list += '<th>ระยะเวลาคุย/วินาที</th>';
                        content_list += '<th>เวลาโทร</th>';
                    content_list += '</tr>';
                    $.each(data, function(i, item){
                        time = item.callTime.substr(11,19);
                        content_list += '<tr>';
                            content_list += '<td>'+(i+1)+'</td>';
                            content_list += '<td>'+item.note+'</td>';
                            content_list += '<td align="right">'+item.oncall+'</td>';
                            content_list += '<td align="right">'+item.ontalk+'</td>';
                            content_list += '<td>'+time+'</td>';
                        content_list += '</tr>';
                    });
                content_list +='</table>';
                Swal.fire({
                    showCancelButton: false,
                    showConfirmButton: false,
                    reverseButtons: false,
                    html:   '<div class="row" style="margin-left: -58px !important; margin-right: -58px !important; margin-top: -15px !important;">'+
                                '<div class="col-lg-12 col-md-12">'+
                                    '<div class="card">'+
                                        '<div class="card-header bg-'+color+'" style="color:#fff;">'+contenttext+'</div>'+
                                        '<div class="card-body text-left" style="font-size:14px;">'+content_list+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                });
            }
        });
    }

    // function tranfer_sending(){
    //     content = "<table class='table data-table' >";
    //         content += "<thead>";
    //             content += "<tr>";
    //                 content += "<th>No</th>";
    //                 content += "<th>หมายเลขพัสดุ</th>";
    //                 content += "<th>ชื่อผู้ส่ง</th>";
    //                 content += "<th>ชื่อผู้รับ</th>";
    //                 content += "<th>COD</th>";
    //                 content += "<th>ผู้นำส่ง</th>";
    //                 content += "<th>เวลาจ่ายงาน</th>";
    //                 content += "<th>ผู้จ่ายงาน</th>";
    //                 content += "<th width='120px'>สถานะงาน</th>";
    //             content += "</tr>";
    //         content += "</thead>";
    //         content += "<tbody>";
    //         content += "</tbody>";
    //     content += "</table>";
    //     $("#tablelist").html(content);
    //     $(function () {
    //         var table = $('.data-table').DataTable({
    //             processing: true,
    //             serverSide: true,
    //             ajax: {
    //                 method:"POST",
    //                 url:"{{url('tranfer_sending_list')}}",
    //                 dataType: 'json',
    //                 data:{
    //                         "_token": "{{ csrf_token() }}",
    //                         "id": "{{$id}}"
    //                     },
    //             },
    //             columns: [
    //                 {data: 'DT_RowIndex', name: 'DT_RowIndex'},
    //                 {data: 'transfer_tracking_id', name: 'transfer_tracking_id'},
    //                 {data: 'cust_send_name', name: 'cust_send_name'},
    //                 {data: 'cust_name', name: 'cust_name'},
    //                 {data: 'subtracking_cod', name: 'subtracking_cod', className:'text-right'},
    //                 {data: 'courier_sending', name: 'courier_sending'},
    //                 {data: 'created_at', name: 'created_at'},
    //                 {data: 'tranfer_by_employee_id', name: 'tranfer_by_employee_id'},
    //                 {data: 'action', name: 'action', orderable: false, searchable: false},
    //             ]
    //         });
    //     });
    // }
</script>

@endsection
