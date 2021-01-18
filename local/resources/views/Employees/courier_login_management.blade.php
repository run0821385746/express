@extends("welcome")
@section("content")

<div class="col-md-12">  
    <div class="main-card mb-6 card">
        <div class="card-header">
            <div class="col-md-6" id="title_header">
                ประวัติการล็อกอิน Service Express Mobie App
            </div>
            <div class="col-md-6">
                <button class="btn btn-success pull-right" onclick="login_stampday('1','')" id="login_stampday_btn">รายการลงเวลาประจำวัน</button>
                <span class="pull-right">&nbsp;</span>
                <button class="btn btn-primary pull-right" style="display: none;" onclick="login_history()" id="login_history_btn">ประวัติล็อกอิน</button>
                <span class="pull-right">&nbsp;</span>
                <span class="form-inline pull-right">
                    <input type="date" class="form-control form-control-sm" id="selectdate" onchange="login_stampday('0', this)" />
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="tablelist">
            
            </div>
        </div>
        <div class="d-block text-left card-footer">
            <div class="row">   
                <div class="col-lg-6 col-md-6"></div>
                <div class="col-lg-6 col-md-6">
                    {{-- <button class="btn-wide  btn btn-success pull-right">Export Documents</button> --}}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    login_history();
    function login_history(){
        $("#title_header").html("ประวัติการล็อกอิน Service Express Mobie App");
        $("#login_stampday_btn").css("display","block");
        $("#login_history_btn").css("display","none");
        $("#selectdate").css("display","none");
        content = "<table class='table data-table' >";
            content += "<thead>";
                content += "<tr>";
                    content += "<th>No</th>";
                    content += "<th>ชื่อพนักงาน</th>";
                    content += "<th>ประเภทผู้เข้าใช้ระบบ</th>";
                    content += "<th>เวลาที่เข้าใช้</th>";
                    content += "<th>สถานะการล็อกอิน</th>";
                    content += "<th>สถานที่ล็อกอิน</th>";
                    content += "<th>ภาพผู้เข้าใช้ระบบ</th>";
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
                    url:"{{url('courier_login_his_datatable')}}",
                    dataType: 'json',
                    data:{
                            "_token": "{{ csrf_token() }}"
                        },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'employee_id', name: 'employee_id'},
                    {data: 'login_type', name: 'login_type'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'login_status', name: 'login_status'},
                    {data: 'lat_long', name: 'lat_long', className: 'text-center'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
                ]
            });
        });
    }
    
    function login_stampday(id, date){
        if(id == 1){
            date = (new Date()).toISOString().split('T')[0];
            $("#login_stampday_btn").css("display","none");
            $("#login_history_btn").css("display","block");
            $("#selectdate").css("display","block");
            $("#selectdate").val(date);
        }else{
            date = date.value;
        }

        $("#title_header").html("ประวัติการล็อกอินลงเวลาประจำวัน Service Express Mobie App");
        content = "<table class='table data-table' >";
            content += "<thead>";
                content += "<tr>";
                    content += "<th>No</th>";
                    content += "<th>ชื่อพนักงาน</th>";
                    content += "<th>ประเภทผู้เข้าใช้ระบบ</th>";
                    content += "<th>เวลาเข้างาน</th>";
                    content += "<th>สถานที่ลงเวลาเข้า</th>";
                    content += "<th>ภาพผู้ลงเวลาเข้า</th>";
                    content += "<th>เวลาออกงาน</th>";
                    content += "<th>สถานที่ลงเวลาออก</th>";
                    content += "<th>ภาพผู้ลงเวลาออก</th>";
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
                    url:"{{url('courier_login_stampDay_datatable')}}",
                    dataType: 'json',
                    data:{
                            "_token": "{{ csrf_token() }}",
                            "date": date,
                        },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'employee_id', name: 'employee_id'},
                    {data: 'login_type', name: 'login_type'},
                    {data: 'login_time', name: 'login_time', orderable: false, searchable: false, className: 'text-center'},
                    {data: 'login_lat_long', name: 'login_lat_long', orderable: false, searchable: false, className: 'text-center'},
                    {data: 'login_img', name: 'login_img', orderable: false, searchable: false, className: 'text-center'},
                    {data: 'logout_time', name: 'logout_time', orderable: false, searchable: false, className: 'text-center'},
                    {data: 'logout_lat_long', name: 'logout_lat_long', orderable: false, searchable: false, className: 'text-center'},
                    {data: 'logout_img', name: 'logout_img', orderable: false, searchable: false, className: 'text-center'}
                ]
            });
        });
    }

    var photo_id = "",
        brn_view = "",
        btn = "";
    function ShowViewPhoto(id, btn, imgPosition){
        html_key = $('#'+btn+id).html();
        if(html_key == 'View'){
            if(photo_id != ""){
                $("#"+photo_id).css('display','none');
                $("#"+brn_view).html('View');
            }
            photo_id = imgPosition+id;
            brn_view = btn+id;
            $("#"+photo_id).css('display','block');
            $("#"+brn_view).html('Close');
        }else if(html_key == 'Close'){
            $("#"+photo_id).css('display','none');
            $("#"+brn_view).html('View');
        }

    }
</script>
@endsection