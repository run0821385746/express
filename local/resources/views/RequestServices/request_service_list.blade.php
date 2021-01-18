@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header"> รายการเรียกรถเข้ารับพัสดุ</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Booking No</th>
                            <th class="text-left">ข้อมูลผู้ส่งพัสดุ</th>
                            <th class="text-center">พนักงานผู้เข้ารับพัสดุ(Courier)</th>
                            <th class="text-center">จำนวนพัสดุ</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">ทำรายการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @if (!empty($requestServiceList))
                            @foreach ($requestServiceList as $requestService)
                            <tr>
                                <td class="text-center">
                                    {{$requestService->request_booking_no}}
                                </td>
                                <td class="text-left">{{$requestService->sender->cust_name}}</td>
                                <td class="text-center">{{$requestService->courier->emp_firstname}} {{$requestService->courier->emp_lastname}}</td>
                                <td class="text-center">
                                    {{$requestService->request_parcel_qty == '1' ? "1 ชิ้น" : null}}
                                    {{$requestService->request_parcel_qty == '2' ? "2 ชิ้น" : null}}
                                    {{$requestService->request_parcel_qty == '3' ? "3 ชิ้น" : null}}
                                    {{$requestService->request_parcel_qty == '4' ? "4 ชิ้น" : null}}
                                    {{$requestService->request_parcel_qty == '5' ? "5-10 ชิ้น" : null}}
                                    {{$requestService->request_parcel_qty == '6' ? "มากกว่า 10 ชิ้น" : null}}
                                    
                                </td>
                                <td class="text-center">{{$requestService->request_status}}</td>
                                <td class="text-center">
                                    <button type="button" id="PopoverCustomT-1"
                                        class="btn btn-primary btn-sm">แสดงรายละเอียด</button>
                                </td>
                            </tr>
                            @endforeach
                        @endif --}}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-block text-left card-footer">
            <div class="row">
                <div class="col-lg-6 col-md-6 text-left">
                    {{-- <nav class="" aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                    aria-label="Previous"><span aria-hidden="true">«</span><span
                                        class="sr-only">Previous</span></a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link">1</a></li>
                            <li class="page-item active"><a href="javascript:void(0);" class="page-link">2</a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link">3</a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                    aria-label="Next"><span aria-hidden="true">»</span><span
                                        class="sr-only">Next</span></a></li>
                        </ul>
                    </nav> --}}
                </div>
                <div class="col-lg-6 col-md-6 text-right">
                    <a href="/request_service">
                    </a>
                    <button class="btn-wide  btn btn-success">Export Documents</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                method:"POST",
                url:"{{url('getRequestServiceListDatatable')}}",
                dataType: 'json',
                data:{
                        "_token": "{{ csrf_token() }}"
                    },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'request_booking_no', name: 'request_booking_no'},
                {data: 'request_sender_id', name: 'request_sender_id'},
                {data: 'request_currier_id', name: 'request_currier_id', className:'text-center'},
                {data: 'request_parcel_qty', name: 'request_parcel_qty', className:'text-center'},
                {data: 'request_status', name: 'request_status'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className:'text-center'},
            ]
        });
    });

    function viewstuck(id, color, status, courier_id, action_status){
        if(action_status !== ""){
            action_status_list = '<div class="card-body text-left" style="font-size:14px;"><b>หมายเหตุ : </b>'+action_status+'</div>';
        }else{
            action_status_list = '';
        }
        $.ajax({
            method:"POST",
            url:"{{url('getfind_call_history_recive')}}",
            dataType: 'json',
            data:{"id":id, "courier_id":courier_id, "_token": "{{ csrf_token() }}"},
            success:function(data){
                // console.log(data);
                    content_list = '<table class="table">';
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
                                            '<div class="card-header bg-'+color+'" style="color:#fff;">'+status+'</div>'+
                                            '<div class="card-body text-left" style="font-size:14px;">'+content_list+
                                            '</div>'+action_status_list+
                                        '</div>'+
                                    '</div>'+
                                '</div>'
                    });
            }
        });
    }
</script>
@endsection