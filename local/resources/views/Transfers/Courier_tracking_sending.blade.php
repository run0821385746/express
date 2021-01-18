@extends("welcome")
@section("content")

<div class="col-md-12">
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
</div>
<script>
    tranfer_sending();
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

    function tranfer_sending(){
        content = "<table class='table data-table' >";
            content += "<thead>";
                content += "<tr>";
                    content += "<th>No</th>";
                    content += "<th>หมายเลขพัสดุ</th>";
                    content += "<th>ชื่อผู้ส่ง</th>";
                    content += "<th>ชื่อผู้รับ</th>";
                    content += "<th>COD</th>";
                    content += "<th>ผู้นำส่ง</th>";
                    content += "<th>เวลาจ่ายงาน</th>";
                    content += "<th>ผู้จ่ายงาน</th>";
                    content += "<th width='120px'>สถานะงาน</th>";
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
                    url:"{{url('tranfer_sending_list')}}",
                    dataType: 'json',
                    data:{
                            "_token": "{{ csrf_token() }}",
                            "id": "{{$id}}"
                        },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'transfer_tracking_id', name: 'transfer_tracking_id'},
                    {data: 'cust_send_name', name: 'cust_send_name'},
                    {data: 'cust_name', name: 'cust_name'},
                    {data: 'subtracking_cod', name: 'subtracking_cod', className:'text-right'},
                    {data: 'courier_sending', name: 'courier_sending'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'tranfer_by_employee_id', name: 'tranfer_by_employee_id'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    }
</script>

@endsection
