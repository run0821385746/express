@extends("welcome")
@section("content")
@php
    $date = date('m-d-Y');
    $date1 = str_replace('-', '/', $date);
    $yesterday = date('Y-m-d',strtotime($date1 . "-1 days"));
    
    $date1 = str_replace('-', '/', $date);
    $yesterday2 = date('Y-m-d',strtotime($date1 . "-2 days"));
@endphp
<div class="col-md-12">  
    <div class="main-card mb-6 card">
        <div class="card-header">
            <div class="col-md-12" id="title_header">
                รายงาน : <span id="showtitle" style="font-size:16px;"></span>
                <span class="form-inline pull-right">
                    สาขา : <select name="dropcenter" class="form-control form-control-sm" id="dropcenter" onchange="sent_request()" style="margin-left:10px; margin-right:10px; @if ($employee->emp_position !== 'เจ้าของกิจการ(Owner)') display:none; @endif">
                        <option value="0">ทั้งหมด</option>
                        @foreach ($DropCenters as $key => $DropCenter)
                            <option value="{{$DropCenter->id}}" @if ($employee->emp_branch_id == $DropCenter->id) selected @endif>{{$DropCenter->drop_center_name_initial}}</option>
                        @endforeach
                    </select>
                    @php
                        $types = array(" -- กรุณาเลือก -- ", "รายการรับเข้า", "รายการนำส่ง DVL", "รายการส่งสำเร็จ", "รายการส่งไม่สำเร็จ", "รายการส่งขึ้น LH", "สรุปยอด COD", "สรุปรายการขายอื่นๆ", "ใบ DVL ย้อนหลัง", "ใบ HL ย้อนหลัง");
                    @endphp

                    รายงาน : <select name="report_type" class="form-control form-control-sm" id="report_type" style="margin-left:10px; margin-right:10px;" onchange="select_report(this)">
                        @foreach ($types as $key => $type)
                            @if ($key !== 0)
                                <option value="{{$key}}" @if ($report_type == $key) selected @endif>{{$type}}</option>
                            @endif
                        @endforeach
                    </select>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12" style="margin-bottom: 15px;">
                    <span class="form-inline pull-right">
                        <button style="margin-left:5px; margin-right:5px;" class="btn btn-warning btn-sm" onclick="day_option_btn('<?= $yesterday2; ?>')">เมื่อวานซืน</button>
                        <button style="margin-left:5px; margin-right:5px;" class="btn btn-info btn-sm" onclick="day_option_btn('<?= $yesterday; ?>')">เมื่อวาน</button>
                        <button style="margin-left:5px; margin-right:5px;" class="btn btn-primary btn-sm" onclick="day_option_btn('<?= date('Y-m-d'); ?>')">วันนี้</button>
                        <button style="margin-left:5px; margin-right:5px;" class="btn btn-light btn-sm" onclick="showselectdate()">เลือกวันที่</button>
                        <span id="selectdate" style="display:none;">
                            วันที่ : <input type="date" class="form-control form-control-sm" style="margin-left:10px; margin-right:10px;" id="selectdateFrom" value="<?= date('Y-m-d'); ?>" />
                            ถึง <input type="date" class="form-control form-control-sm" style="margin-left:10px; margin-right:10px;" id="selectdateTo" value="<?= date('Y-m-d'); ?>" />
                            <button style="margin-left:5px; margin-right:5px;" class="btn btn-success btn-sm" id="submit_request" onclick="sent_request()">แสดงข้อมูล</button>
                        </span>
                    </span>
                </div>
            </div>
            <div class="table-responsive" id="repost_list" style="margin-left:-2.5px; margin-right:-2.5px;">
                    
            </div>
        </div>
        <div class="d-block text-left card-footer">
            <div class="row">   
                <div class="col-lg-6 col-md-6"></div>
                <div class="col-lg-6 col-md-6">
                    <form action="{{url('print_report')}}" method="post" target="_blank">
                        {{csrf_field()}}
                        <input type="hidden" name="dropcenter_pdf" id="dropcenter_pdf">
                        <input type="hidden" name="report_type_pdf" id="report_type_pdf">
                        <input type="hidden" name="selectdateFrom_pdf" id="selectdateFrom_pdf">
                        <input type="hidden" name="selectdateTo_pdf" id="selectdateTo_pdf">
                        <button class="btn-wide btn btn-success pull-right" id="print_report_submit" type="submit" onclick="prunt_pdf()">PDF Print</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    title = $("select#report_type > option[selected]").text();
    $("#showtitle").html(title);
    function select_report(selection){
        value = selection.value;
        title = $("select#report_type > option[value="+value+"]").text();
        $("#showtitle").html(title);
        sent_request();
    }

    function showselectdate(){
        $("#selectdate").css("display", "block");
    }

    function hidetdate(){
        $("#selectdate").css("display", "none");
    }

    function day_option_btn(date){
        $("#selectdateFrom").val(date);
        $("#selectdateTo").val(date);
        hidetdate();
        $("#submit_request").trigger("click");

    }

    sent_request();
    function sent_request(){
        $("#print_report_submit").prop("disabled", false);
        dropcenter = $("#dropcenter").val();
        report_type = $("#report_type").val();
        selectdateFrom = $("#selectdateFrom").val();
        selectdateTo = $("#selectdateTo").val();

        $("#dropcenter_pdf").val(dropcenter);
        $("#report_type_pdf").val(report_type);
        $("#selectdateFrom_pdf").val(selectdateFrom);
        $("#selectdateTo_pdf").val(selectdateTo);
        if(report_type == '9'){
            $("#print_report_submit").prop("disabled", true);
            content = "<table class='table data-table' >";
                content += "<thead>";
                    content += "<tr>";
                        content += "<th class='text-center'>ลำดับ</th>";
                        content += "<th class='text-center'>รหัส LH</th>";;
                        content += "<th class='text-right'>เวลาทำรายการ</th>";
                        content += "<th class='text-right'>ทำรายการโดย</th>";
                        content += "<th class='text-center'>หมายเหตุ</th>";
                        content += "<th class='text-center'>ทำรายการ</th>";
                    content += "</tr>";
                content += "</thead>";
                content += "<tbody>";
                content += "</tbody>";
            content += "</table>";
            $("#repost_list").html(content);
            $(function () {
                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        method:"POST",
                        url:"{{url('report_request')}}",
                        dataType: 'json',
                        data:{
                                "_token": "{{ csrf_token() }}",
                                "dropcenter": $("#dropcenter").val(),
                                "report_type": $("#report_type").val(),
                                "selectdateFrom": $("#selectdateFrom").val(),
                                "selectdateTo": $("#selectdateTo").val()
                            },
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'transfer_bill_no', name: 'transfer_bill_no'},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'tranfer_employee_sender_id', name: 'tranfer_employee_sender_id'},
                        {data: 'note', name: 'note'},
                        {data: 'action', name: 'action', orderable: false, searchable: false, className:'text-center'}
                    ]
                });
            });

        }else if(report_type == '8'){
            $("#print_report_submit").prop("disabled", true);
            content = "<table class='table data-table' >";
                content += "<thead>";
                    content += "<tr>";
                        content += "<th class='text-center'>ลำดับ</th>";
                        content += "<th class='text-center'>รหัสใบนำส่ง</th>";
                        content += "<th class='text-left'>ผู้นำส่ง</th>";
                        content += "<th class='text-left'>ทะเบียนรถนำส่ง</th>";
                        content += "<th class='text-center'>เบอร์ติดต่อ</th>";
                        content += "<th class='text-center'>ผู้จ่ายงาน</th>";
                        content += "<th class='text-right'>สถานะ</th>";
                        content += "<th class='text-right'>ยอด COD</th>";
                        content += "<th class='text-right'>เวลาจ่ายงาน</th>";
                        content += "<th class='text-center'>ทำรายการ</th>";
                    content += "</tr>";
                content += "</thead>";
                content += "<tbody>";
                content += "</tbody>";
            content += "</table>";
            $("#repost_list").html(content);
            $(function () {
                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        method:"POST",
                        url:"{{url('report_request')}}",
                        dataType: 'json',
                        data:{
                                "_token": "{{ csrf_token() }}",
                                "dropcenter": $("#dropcenter").val(),
                                "report_type": $("#report_type").val(),
                                "selectdateFrom": $("#selectdateFrom").val(),
                                "selectdateTo": $("#selectdateTo").val()
                            },
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
                        {data: 'transfer_bill_no', name: 'transfer_bill_no'},
                        {data: 'transfer_bill_courier_id', name: 'transfer_bill_courier_id'},
                        {data: 'tranfer_driver_sender_numberplate', name: 'tranfer_driver_sender_numberplate'},
                        {data: 'courierPhone', name: 'courierPhone'},
                        {data: 'tranfer_by_employee_id', name: 'tranfer_by_employee_id'},
                        {data: 'transfer_bill_status', name: 'transfer_bill_status', className:'text-center'},
                        {data: 'CODamount', name: 'CODamount', className:'text-right'},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'action', name: 'action', orderable: false, searchable: false, className:'text-center'}
                    ]
                });
                var table = $('.data-table').DataTable();
                $('.data-table').on('draw.dt', function () {
                    if (table.data().any()  && table.column(5).data().length > 1) {
                        
                        var CODamount_sum = table.column(7).data().reduce(myFunc);
                        if($("span#show_sum_result").html() == null){
                            $( ".data-table" ).after( '<span class="pull-right" id="show_sum_result" style="padding-right:15px; width:40%; margin-left:-15px;"><div class="row"><div class="col-lg-4 col-md-4"></div><div class="col-lg-4 col-md-4"></div><div class="col-lg-4 col-md-4"><span class="pull-right">รวม COD</span><br><span class="pull-right">'+CODamount_sum.toFixed(2)+'</span></div></div></span>');
                        }else{
                            $("span#show_sum_result").html('<div class="row"><div class="col-lg-4 col-md-4"></div><div class="col-lg-4 col-md-4"></div><div class="col-lg-4 col-md-4"><span class="pull-right">รวม COD</span><br><span class="pull-right">'+CODamount_sum.toFixed(2)+'</span></div></div>');
                        }
                    }
                });
            });
        }else if(report_type == '7'){
            content = "<table class='table data-table' >";
                content += "<thead>";
                    content += "<tr>";
                        content += "<th>ลำดับ</th>";
                        content += "<th>Booking No.</th>";
                        content += "<th>สินค้า</th>";
                        content += "<th>ราคา</th>";
                        content += "<th>วันเวลาดำเนินการ</th>";
                        content += "<th>หมายเหตุ</th>";
                    content += "</tr>";
                content += "</thead>";
                content += "<tbody>";
                content += "</tbody>";
            content += "</table>";
            $("#repost_list").html(content);
            $(function () {
                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        method:"POST",
                        url:"{{url('report_request')}}",
                        dataType: 'json',
                        data:{
                                "_token": "{{ csrf_token() }}",
                                "dropcenter": $("#dropcenter").val(),
                                "report_type": $("#report_type").val(),
                                "selectdateFrom": $("#selectdateFrom").val(),
                                "selectdateTo": $("#selectdateTo").val()
                            },
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
                        {data: 'booking_no', name: 'booking_no'},
                        {data: 'product_name', name: 'product_name'},
                        {data: 'product_price', name: 'product_price', className:"text-right", searchable: false},
                        {data: 'datetime', name: 'datetime', className:"text-right", orderable: false},
                        {data: 'action', name: 'action', orderable: false}
                    ]
                });

                var table = $('.data-table').DataTable();
                $('.data-table').on('draw.dt', function () {
                    if (table.data().any() && table.column(5).data().length > 1){
                        
                        var product_price_sum = table.column(3).data().reduce(myFunc);
                        if($("span#show_sum_result").html() == null){
                            $( ".data-table" ).after( '<span class="pull-right" id="show_sum_result" style="padding-right:15px; width:40%; margin-left:-15px;"><div class="row"><div class="col-lg-4 col-md-4"></div><div class="col-lg-4 col-md-4"></div><div class="col-lg-4 col-md-4"><span class="pull-right">รวมยอด</span><br><span class="pull-right">'+product_price_sum.toFixed(2)+'</span></div></div></span>');
                        }else{
                            $("span#show_sum_result").html('<div class="row"><div class="col-lg-4 col-md-4"></div><div class="col-lg-4 col-md-4"></div><div class="col-lg-4 col-md-4"><span class="pull-right">รวมยอด</span><br><span class="pull-right">'+product_price_sum.toFixed(2)+'</span></div></div>');
                        }
                    }
                });
            });
        }else{
            content = "<table class='table data-table' >";
                content += "<thead>";
                    content += "<tr>";
                        content += "<th>ลำดับ</th>";
                        content += "<th>Booking No.</th>";
                        content += "<th>Tracking No.</th>";
                        content += "<th>จำนวนพัสดุ/กล่อง</th>";
                        content += "<th>ค่าจัดส่ง</th>";
                        content += "<th>COD</th>";
                        content += "<th>วันเวลาดำเนินการ</th>";
                        content += "<th>หมายเหตุ</th>";
                    content += "</tr>";
                content += "</thead>";
                content += "<tbody>";
                content += "</tbody>";
            content += "</table>";
            $("#repost_list").html(content);
            $(function () {
                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        method:"POST",
                        url:"{{url('report_request')}}",
                        dataType: 'json',
                        data:{
                                "_token": "{{ csrf_token() }}",
                                "dropcenter": $("#dropcenter").val(),
                                "report_type": $("#report_type").val(),
                                "selectdateFrom": $("#selectdateFrom").val(),
                                "selectdateTo": $("#selectdateTo").val()
                            },
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
                        {data: 'booking_no', name: 'booking_no'},
                        {data: 'tracking_no', name: 'tracking_no'},
                        {data: 'parcel_amount', name: 'parcel_amount', className:"text-right", searchable: false},
                        {data: 'shipping_fee', name: 'shipping_fee', className:"text-right", searchable: false},
                        {data: 'cod_amount', name: 'cod_amount', className:"text-right", searchable: false},
                        {data: 'datetime', name: 'datetime', className:"text-right", orderable: false},
                        {data: 'action', name: 'action', orderable: false}
                    ]
                });
                var table = $('.data-table').DataTable();
                $('.data-table').on('draw.dt', function () {
                    if (table.data().any() && table.column(5).data().length > 1){
                        
                        var parcel_amount_sum = table.column(3).data().reduce(myFunc);
                        var shipping_fee_sum = table.column(4).data().reduce(myFunc);
                        var cod_amount_sum = table.column(5).data().reduce(myFunc);
                        if($("span#show_sum_result").html() == null){
                            $( ".data-table" ).after( '<span class="pull-right" id="show_sum_result" style="padding-right:15px; width:40%; margin-left:-15px;"><div class="row"><div class="col-lg-4 col-md-4"><span class="pull-right">รวมจำนวนพัสดุ/กล่อง</span><br><span class="pull-right">'+parcel_amount_sum+'</span></div><div class="col-lg-4 col-md-4"><span class="pull-right">รวมค่าจัดส่ง</span><br><span class="pull-right">'+shipping_fee_sum.toFixed(2)+'</span></div><div class="col-lg-4 col-md-4"><span class="pull-right">รวมCOD</span><br><span class="pull-right">'+cod_amount_sum.toFixed(2)+'</span></div></div></span>');
                        }else{
                            $("span#show_sum_result").html('<div class="row"><div class="col-lg-4 col-md-4"><span class="pull-right">รวมจำนวนพัสดุ/กล่อง</span><br><span class="pull-right">'+parcel_amount_sum+'</span></div><div class="col-lg-4 col-md-4"><span class="pull-right">รวมค่าจัดส่ง</span><br><span class="pull-right">'+shipping_fee_sum.toFixed(2)+'</span></div><div class="col-lg-4 col-md-4"><span class="pull-right">รวมCOD</span><br><span class="pull-right">'+cod_amount_sum.toFixed(2)+'</span></div></div>');
                        }
                    }
                });
            });
        }
    }

    function prunt_pdf(){

    }

    function myFunc(total, num) {
        totle_sum = parseInt(total) + parseInt(num)
        return totle_sum;
    }

    // login_history();
    // function login_history(){
    //     $("#title_header").html("ประวัติการล็อกอิน Service Express Mobie App");
    //     $("#login_stampday_btn").css("display","block");
    //     $("#login_history_btn").css("display","none");
    //     $("#selectdate").css("display","none");
    //     content = "<table class='table data-table' >";
    //         content += "<thead>";
    //             content += "<tr>";
    //                 content += "<th>No</th>";
    //                 content += "<th>ชื่อพนักงาน</th>";
    //                 content += "<th>ประเภทผู้เข้าใช้ระบบ</th>";
    //                 content += "<th>เวลาที่เข้าใช้</th>";
    //                 content += "<th>สถานะการล็อกอิน</th>";
    //                 content += "<th>สถานที่ล็อกอิน</th>";
    //                 content += "<th>ภาพผู้เข้าใช้ระบบ</th>";
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
    //                 url:"{{url('courier_login_his_datatable')}}",
    //                 dataType: 'json',
    //                 data:{
    //                         "_token": "{{ csrf_token() }}"
    //                     },
    //             },
    //             columns: [
    //                 {data: 'DT_RowIndex', name: 'DT_RowIndex'},
    //                 {data: 'employee_id', name: 'employee_id'},
    //                 {data: 'login_type', name: 'login_type'},
    //                 {data: 'updated_at', name: 'updated_at'},
    //                 {data: 'login_status', name: 'login_status'},
    //                 {data: 'lat_long', name: 'lat_long', className: 'text-center'},
    //                 {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
    //             ]
    //         });
    //     });
    // }
    
    // function login_stampday(id, date){
    //     if(id == 1){
    //         date = (new Date()).toISOString().split('T')[0];
    //         $("#login_stampday_btn").css("display","none");
    //         $("#login_history_btn").css("display","block");
    //         $("#selectdate").css("display","block");
    //         $("#selectdate").val(date);
    //     }else{
    //         date = date.value;
    //     }

    //     $("#title_header").html("ประวัติการล็อกอินลงเวลาประจำวัน Service Express Mobie App");
    //     content = "<table class='table data-table' >";
    //         content += "<thead>";
    //             content += "<tr>";
    //                 content += "<th>No</th>";
    //                 content += "<th>ชื่อพนักงาน</th>";
    //                 content += "<th>ประเภทผู้เข้าใช้ระบบ</th>";
    //                 content += "<th>เวลาเข้างาน</th>";
    //                 content += "<th>สถานที่ลงเวลาเข้า</th>";
    //                 content += "<th>ภาพผู้ลงเวลาเข้า</th>";
    //                 content += "<th>เวลาออกงาน</th>";
    //                 content += "<th>สถานที่ลงเวลาออก</th>";
    //                 content += "<th>ภาพผู้ลงเวลาออก</th>";
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
    //                 url:"{{url('courier_login_stampDay_datatable')}}",
    //                 dataType: 'json',
    //                 data:{
    //                         "_token": "{{ csrf_token() }}",
    //                         "date": date,
    //                     },
    //             },
    //             columns: [
    //                 {data: 'DT_RowIndex', name: 'DT_RowIndex'},
    //                 {data: 'employee_id', name: 'employee_id'},
    //                 {data: 'login_type', name: 'login_type'},
    //                 {data: 'login_time', name: 'login_time', orderable: false, searchable: false, className: 'text-center'},
    //                 {data: 'login_lat_long', name: 'login_lat_long', orderable: false, searchable: false, className: 'text-center'},
    //                 {data: 'login_img', name: 'login_img', orderable: false, searchable: false, className: 'text-center'},
    //                 {data: 'logout_time', name: 'logout_time', orderable: false, searchable: false, className: 'text-center'},
    //                 {data: 'logout_lat_long', name: 'logout_lat_long', orderable: false, searchable: false, className: 'text-center'},
    //                 {data: 'logout_img', name: 'logout_img', orderable: false, searchable: false, className: 'text-center'}
    //             ]
    //         });
    //     });
    // }

    // var photo_id = "",
    //     brn_view = "",
    //     btn = "";
    // function ShowViewPhoto(id, btn, imgPosition){
    //     html_key = $('#'+btn+id).html();
    //     if(html_key == 'View'){
    //         if(photo_id != ""){
    //             $("#"+photo_id).css('display','none');
    //             $("#"+brn_view).html('View');
    //         }
    //         photo_id = imgPosition+id;
    //         brn_view = btn+id;
    //         $("#"+photo_id).css('display','block');
    //         $("#"+brn_view).html('Close');
    //     }else if(html_key == 'Close'){
    //         $("#"+photo_id).css('display','none');
    //         $("#"+brn_view).html('View');
    //     }

    // }
</script>
@endsection