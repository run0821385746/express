
@extends("welcome")
@section("content")
<style>
    .swal-wide{
        width:850px !important;
    }
</style>
<div class="app-main__inner">
    <div class="row">
        <div class="col-md-6 col-xl-2">
            <div class="card mb-3 widget-content" onclick="ckeckcon('CLS')" style="background-color: rgb(118, 205, 255); cursor: pointer;">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading"><i class="text-white fa fa-cubes" style="font-size: 22px;" aria-hidden="true"></i></div>
                            <div class="widget-subheading"><b style="color:#FFF !important;">CLS</b></div>
                        </div>
                        <div class="widget-content-right">
                            <div style="font-size:20px;" class="text-white" id="CLS">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card mb-3 widget-content" onclick="ckeckcon('CONS')" style="background-color: rgb(82, 139, 172); cursor: pointer;">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading"><i class="text-white fa fa-cubes" style="font-size: 22px;" aria-hidden="true"></i></div>
                            <div class="widget-subheading"><b style="color:#FFF !important;">CON</b></div>
                        </div>
                        <div class="widget-content-right">
                            <div style="font-size:20px;" class="text-white" id="CONS">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card mb-3 widget-content" onclick="ckeckcon('POD')" style="cursor: pointer; background-color: rgb(61, 158, 98);">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading"><i class="text-white fa fa-smile-o" style="font-size: 22px; font-weight:bold;" aria-hidden="true"></i></div>
                            <div class="widget-subheading"><b style="color:#FFF !important;">POD</b></div>
                        </div>
                        <div class="widget-content-right">
                            <div style="font-size:20px;" class="text-white" id="POD">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card mb-3 widget-content" onclick="ckeckcon('DLY')" style="cursor: pointer; background-color: rgb(184, 161, 58);">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading"><i class="text-white fa fa-frown-o" style="font-size: 22px; font-weight:bold;" aria-hidden="true"></i></div>
                            <div class="widget-subheading"><b style="color:#FFF !important;">DLY</b></div>
                        </div>
                        <div class="widget-content-right">
                            <div style="font-size:20px;" class="text-white" id="DLY">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card mb-3 widget-content" onclick="ckeckcon('COD')" style="cursor: pointer; background-color: rgb(53, 209, 188);">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading"><i class="text-white fa fa-usd" style="font-size: 22px; font-weight:bold;" aria-hidden="true"></i></div>
                            <div class="widget-subheading"><b style="color:#FFF !important;">COD</b></div>
                        </div>
                        <div class="widget-content-right">
                            <div style="font-size:20px;" class="text-white" id="COD" align="right">0</div>
                            <div style="font-size:16px; border-top:1px solid #000; margin-bottom: -10px;" class="text-white" id="COD_ALL" align="right">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card mb-3 widget-content" onclick="ckeckcon('LH')" style="cursor: pointer; background-color: rgb(25, 136, 201);">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading"><i class="text-white fa fa-truck" style="font-size: 22px;" aria-hidden="true"></i></div>
                            <div class="widget-subheading"><b style="color:#FFF !important;">LH</b></div>
                        </div>
                        <div class="widget-content-right">
                            <div style="font-size:20px;" class="text-white" id="LH">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card mb-3 widget-content" onclick="ckeckcon('on_LH')" style="cursor: pointer; background-color: rgb(25, 136, 201);">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading"><i class="text-white fa fa-truck" style="font-size: 22px;" aria-hidden="true"></i></div>
                            <div class="widget-subheading"><b style="color:#FFF !important;">ON LH</b></div>
                        </div>
                        <div class="widget-content-right">
                            <div style="font-size:20px;" class="text-white" id="on_LH">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card mb-3 widget-content" onclick="ckeckcon('DVL')" style="cursor: pointer; background-color: rgb(247, 89, 89);">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading"><i class="text-white fa fa-motorcycle" style="font-size: 22px;" aria-hidden="true"></i></div>
                            <div class="widget-subheading"><b style="color:#FFF !important;">DVL</b></div>
                        </div>
                        <div class="widget-content-right">
                            <div style="font-size:20px;" class="text-white"id="DVL">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <a href="/Courier_cod_closing/{{$employee->emp_branch_id}}" style="text-decoration: none;">
                <div class="card mb-3 widget-content" onclick="" style="cursor: pointer; background-color: rgb(28, 206, 22);">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading"><i class="text-white fa fa-file-text-o" style="font-size: 22px;" aria-hidden="true"></i></div>
                                <div class="widget-subheading"><b style="color:#FFF !important;">Delivery bill</b></div>
                            </div>
                            <div class="widget-content-right">
                                <div style="font-size:20px;" class="text-white" id="tranfer_bill">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- <div class="d-xl-none d-lg-block col-md-6 col-xl-4">
            <div class="card mb-3 widget-content">
                <div class="widget-content-outer">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading">Income</div>
                            <div class="widget-subheading">Expected totals</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-focus">$147</div>
                        </div>
                    </div>
                    <div class="widget-progress-wrapper">
                        <div class="progress-bar-sm progress-bar-animated-alt progress">
                            <div class="progress-bar bg-info" role="progressbar" aria-valuenow="54" aria-valuemin="0"
                                aria-valuemax="100" style="width: 54%;"></div>
                        </div>
                        <div class="progress-sub-label">
                            <div class="sub-label-left">Expenses</div>
                            <div class="sub-label-right">100%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
    <div class="row">
        <div class="col-md-12 col-xl-6">
            <div class="card">
                <div class="card-header" style="background-color: rgb(247, 89, 89);">DVL Courier List</div>
                <div class="card-body" style="background-color: rgb(255, 255, 255);" id="courierDVLlist"></div>
            </div>
        </div>
        <div class="col-md-12 col-xl-6" id="commingtoDC">
            <br>
        </div>
        <div class="col-md-12 col-xl-6">
            @php
                // $user->isOnline();
            @endphp
            {{-- @if($user->isOnline())
                user is online!!
            @endif --}}
        </div>
        @if ($employee->emp_branch_id == null)
            <div class="col-md-12 col-xl-12" style="margin-left:-15px;  top:-270px;">
                <div style="position:absolute; background-color:rgba(68, 66, 66, 0.493); height:280px; width:100%; color:#fff; font-size:20px; border-radius:5px;" align="center">
                    <br>
                    <br>
                    <br>
                    <br>
                    กรุณาเลือกสาขาเพื่อดูรายละเอียด
                </div>
            </div>
        @endif
    </div>

    {{-- {{dd($employee->emp_branch_id)}} --}}
    {{-- <div class="row">
        <div class="col-md-12 col-lg-6">
            <div class="mb-3 card">
                <div class="card-header-tab card-header-tab-animation card-header">
                    <div class="card-header-title">
                        <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                        ภาพรวมรายงานรายรับ
                    </div>

                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabs-eg-77">
                            <div class="card mb-3 widget-chart widget-chart2 text-left w-100">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 m-0">
                                        <canvas id="canvas"></canvas>
                                    </div>
                                </div>
                            </div>
                            <h6 class="text-muted text-uppercase font-size-md opacity-5 font-weight-normal">Top
                                Authors</h6>
                            <div class="scroll-area-sm">
                                <div class="scrollbar-container">
                                    <ul class="rm-list-borders rm-list-borders-scroll list-group list-group-flush">
                                        <li class="list-group-item">
                                            <div class="widget-content p-0">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left mr-3">
                                                        <img width="42" class="rounded-circle"
                                                            src="assets/images/avatars/9.jpg" alt="">
                                                    </div>
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">Ella-Rose Henry</div>
                                                        <div class="widget-subheading">Web Developer</div>
                                                    </div>
                                                    <div class="widget-content-right">
                                                        <div class="font-size-xlg text-muted">
                                                            <small class="opacity-5 pr-1">$</small>
                                                            <span>129</span>
                                                            <small class="text-danger pl-2">
                                                                <i class="fa fa-angle-down"></i>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="widget-content p-0">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left mr-3">
                                                        <img width="42" class="rounded-circle"
                                                            src="assets/images/avatars/5.jpg" alt="">
                                                    </div>
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">Ruben Tillman</div>
                                                        <div class="widget-subheading">UI Designer</div>
                                                    </div>
                                                    <div class="widget-content-right">
                                                        <div class="font-size-xlg text-muted">
                                                            <small class="opacity-5 pr-1">$</small>
                                                            <span>54</span>
                                                            <small class="text-success pl-2">
                                                                <i class="fa fa-angle-up"></i>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="widget-content p-0">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left mr-3">
                                                        <img width="42" class="rounded-circle"
                                                            src="assets/images/avatars/4.jpg" alt="">
                                                    </div>
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">Vinnie Wagstaff</div>
                                                        <div class="widget-subheading">Java Programmer</div>
                                                    </div>
                                                    <div class="widget-content-right">
                                                        <div class="font-size-xlg text-muted">
                                                            <small class="opacity-5 pr-1">$</small>
                                                            <span>429</span>
                                                            <small class="text-warning pl-2">
                                                                <i class="fa fa-dot-circle"></i>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">Courier Dalivery Reports</h5>
                    <canvas id="doughnut-chart"></canvas>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="card-shadow-danger mb-3 widget-chart widget-chart2 text-left card">
                <div class="widget-content">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left pr-2 fsize-1">
                                <div class="widget-numbers mt-0 fsize-3 text-danger">71%</div>
                            </div>
                            <div class="widget-content-right w-100">
                                <div class="progress-bar-xs progress">
                                    <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="71"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 71%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-left fsize-1">
                            <div class="text-muted opacity-6">Courier 1</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card-shadow-success mb-3 widget-chart widget-chart2 text-left card">
                <div class="widget-content">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left pr-2 fsize-1">
                                <div class="widget-numbers mt-0 fsize-3 text-success">54%</div>
                            </div>
                            <div class="widget-content-right w-100">
                                <div class="progress-bar-xs progress">
                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="54"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 54%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-left fsize-1">
                            <div class="text-muted opacity-6">Courier 2</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card-shadow-warning mb-3 widget-chart widget-chart2 text-left card">
                <div class="widget-content">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left pr-2 fsize-1">
                                <div class="widget-numbers mt-0 fsize-3 text-warning">32%</div>
                            </div>
                            <div class="widget-content-right w-100">
                                <div class="progress-bar-xs progress">
                                    <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="32"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 32%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-left fsize-1">
                            <div class="text-muted opacity-6">Courier 3</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card-shadow-info mb-3 widget-chart widget-chart2 text-left card">
                <div class="widget-content">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left pr-2 fsize-1">
                                <div class="widget-numbers mt-0 fsize-3 text-info">89%</div>
                            </div>
                            <div class="widget-content-right w-100">
                                <div class="progress-bar-xs progress">
                                    <div class="progress-bar bg-info" role="progressbar" aria-valuenow="89"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 89%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-left fsize-1">
                            <div class="text-muted opacity-6">Courier 4</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>
<script>
    var DropCenters = <?= $DropCenters; ?>,
        countEmpty = 0;
    setInterval(() => {
        counttrack();
    }, 600000);
    counttrack();
    function counttrack(){
        $.post("{{url('track_result')}}",
            {
                _token: "{{ csrf_token() }}"
            },
            function(data){
                result = JSON.parse(data);
                // console.log(data);
                $("#CLS").html(result.CLS);
                $("#CONS").html(result.CONS);
                $("#POD").html(result.POD);
                $("#DLY").html(result.DLY);
                $("#COD").html(result.COD);
                $("#COD_ALL").html(result.COD_ALL);
                $("#LH").html(result.LH);
                $("#on_LH").html(result.on_LH);
                $("#DVL").html(result.DVL);
                $("#tranfer_bill").html(result.tranfer_bill);
            }
        );
    }
    
    setInterval(() => {
        commingtoDC();
    }, 1800000);
    commingtoDC();
    function commingtoDC(){
        $.post("{{url('commingto_dc')}}",
            {
                _token: "{{ csrf_token() }}"
            },
            function(data){
                // result = JSON.parse(data);
                // console.log(data);
                $("#commingtoDC").html(data);
                // $.each(data function(i, item){
                //     alert(i);
                // });
            }
        );
    }

    function ckeckcon(type, emp_id, bill_id){
        if(type == 'DVL_Contect' || type == 'DVL_CON' || type == 'DVL_POD' || type == 'DVL_DLY' || type == 'DVL_COD'){
            if(type == 'DVL_Contect'){
                content = "<table class='table data-table' >";
                    content += "<thead>";
                        content += "<tr>";
                            content += "<th>ชื่อผู้นำส่ง</th>";
                            content += "<th>เบอร์มือถือ</th>";
                            content += "<th>ทะเบียนรถนำส่ง</th>";
                            content += "<th>เวลาเปิดบิลนำส่ง</th>";
                        content += "</tr>";
                    content += "</thead>";
                    content += "<tbody>";
                    content += "</tbody>";
                content += "</table>";
                Swal.fire({
                    customClass: 'swal-wide',
                    showCancelButton: false,
                    showConfirmButton: false,
                    reverseButtons: false,
                    html:   '<div class="row" style="margin-left: -58px !important; margin-right: -58px !important; margin-top: -15px !important;">'+
                                '<div class="col-lg-12 col-md-12">'+
                                    '<div class="card">'+
                                        '<div class="card-header" style="color:#fff; background-color:rgb(247, 89, 89);">รายละเอียดผู้ส่ง</div>'+
                                        '<div class="card-body text-left" style="font-size:14px;">'+
                                            content+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                });
                // $("#courierDVLlist").html(content);
                $(function () {
                    var table = $('.data-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            method:"POST",
                            url:"{{url('sender_detail_dashboard')}}",
                            dataType: 'json',
                            data:{
                                    "_token": "{{ csrf_token() }}",
                                    "emp_id": emp_id,
                                    "bill_id": bill_id,
                                    "type":type
                                },
                        },
                        columns: [
                            {data: 'sender_name', name: 'sender_name'},
                            {data: 'sander_phone', name: 'sander_phone', className:"text-center"},
                            {data: 'number_plate', name: 'number_plate', className:"text-center"},
                            {data: 'Bill_create_time', name: 'Bill_create_time', className:"text-center"}
                        ]
                    });
                });
            }else{
                if(type == "DVL_COD"){
                    last_collum = "COD";
                    align = "text-right";
                }else{
                    last_collum = "DC ยิงจ่ายเวลา";
                    align = "text-center";
                }
                content = "<table class='table data-table' >";
                    content += "<thead>";
                        content += "<tr>";
                            content += "<th>No</th>";
                            content += "<th>หมายเลขพัสดุ</th>";
                            content += "<th>ชื่อผู้รับ</th>";
                            content += "<th>"+last_collum+"</th>";
                        content += "</tr>";
                    content += "</thead>";
                    content += "<tbody>";
                    content += "</tbody>";
                content += "</table>";
                Swal.fire({
                    customClass: 'swal-wide',
                    showCancelButton: false,
                    showConfirmButton: false,
                    reverseButtons: false,
                    html:   '<div class="row" style="margin-left: -58px !important; margin-right: -58px !important; margin-top: -15px !important;">'+
                                '<div class="col-lg-12 col-md-12">'+
                                    '<div class="card">'+
                                        '<div class="card-header" style="color:#fff; background-color:rgb(247, 89, 89);">รายละเอียดผู้ส่ง</div>'+
                                        '<div class="card-body text-left" style="font-size:14px;">'+
                                            content+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                });
                // $("#courierDVLlist").html(content);
                $(function () {
                    var table = $('.data-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            method:"POST",
                            url:"{{url('sender_detail_dashboard')}}",
                            dataType: 'json',
                            data:{
                                    "_token": "{{ csrf_token() }}",
                                    "emp_id": emp_id,
                                    "bill_id": bill_id,
                                    "type":type
                                },
                        },
                        columns: [
                            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                            {data: 'tracking_no', name: 'tracking_no'},
                            {data: 'recive_Name', name: 'recive_Name', className:"text-left"},
                            {data: 'action_time', name: 'action_time', className:align}
                        ]
                    });
                });
            }
        }else{
            thfour = 'เวลาที่รับงาน';
            if(type == 'CLS'){
                bg = 'rgb(118, 205, 255)';
            }else if(type == 'CONS'){
                bg = 'rgb(82, 139, 172)';
            }else if(type == 'POD'){
                bg = 'rgb(61, 158, 98)';
            }else if(type == 'DLY'){
                bg = 'rgb(184, 161, 58)';
            }else if(type == 'COD'){
                thfour = 'ยอดCOD';
                bg = 'rgb(53, 209, 188)';
            }else if(type == 'LH'){
                bg = 'rgb(25, 136, 201)';
            }else if(type == 'on_LH'){
                bg = 'rgb(25, 136, 201)';
            }else if(type == 'DVL'){
                bg = 'rgb(247, 89, 89)';
            }
            @if ($employee->emp_branch_id !== null && $employee->emp_branch_id !== '')
                content = "<table class='table data-table' >";
                    content += "<thead>";
                        content += "<tr>";
                            content += "<th>No</th>";
                            content += "<th>หมายเลขพัสดุ</th>";
                            content += "<th>รายละเอียดผู้รับ</th>";
                            content += "<th>สถานะงาน</th>";
                            content += "<th >"+thfour+"</th>";
                        content += "</tr>";
                    content += "</thead>";
                    content += "<tbody>";
                    content += "</tbody>";
                content += "</table>";
                Swal.fire({
                    customClass: 'swal-wide',
                    showCancelButton: false,
                    showConfirmButton: false,
                    reverseButtons: false,
                    html:   '<div class="row" style="margin-left: -58px !important; margin-right: -58px !important; margin-top: -15px !important;">'+
                                '<div class="col-lg-12 col-md-12">'+
                                    '<div class="card">'+
                                        '<div class="card-header" style="color:#fff; background-color:'+bg+';">'+type+'</div>'+
                                        '<div class="card-body text-left" style="font-size:14px;">'+
                                            content+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                });
                if(type == 'COD'){
                    $(function () {
                        var table = $('.data-table').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                method:"POST",
                                url:"{{url('track_Detail_from_result')}}",
                                dataType: 'json',
                                data:{
                                        "_token": "{{ csrf_token() }}",
                                        "type": type
                                    },
                            },
                            columns: [
                                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                                {data: 'tracking_no', name: 'tracking_no'},
                                {data: 'senderName', name: 'senderName'},
                                {data: 'tracking_status', name: 'tracking_status'},
                                {data: 'created_at', name: 'created_at', className:"text-right"}
                            ]
                        });
                    });
                }else{
                    $(function () {
                        var table = $('.data-table').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                method:"POST",
                                url:"{{url('track_Detail_from_result')}}",
                                dataType: 'json',
                                data:{
                                        "_token": "{{ csrf_token() }}",
                                        "type": type
                                    },
                            },
                            columns: [
                                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                                {data: 'tracking_no', name: 'tracking_no'},
                                {data: 'senderName', name: 'senderName'},
                                {data: 'tracking_status', name: 'tracking_status'},
                                {data: 'created_at', name: 'created_at'}
                            ]
                        });
                    });
                }
            @else
                countEmpty = 0;
                content = "";
                    $.each(DropCenters, function(index, value){
                        content += '<div id="showdatatable'+value.id+'">';
                            if(index == 0){
                                content += "<div style='padding:10px 15px; background-color:#ccc; text-align:center; font-weight: bold; color:#2874A6;'>"+value.drop_center_name_initial+"</div><hr>";
                            }else{
                                content += "<div id='title_id_"+value.id+"' style='padding:10px 15px; background-color:#ccc; text-align:center; margin-top:50px; font-weight: bold; color:#2874A6;'>"+value.drop_center_name_initial+"</div><hr>";
                            }
                            content += "<table class='table data-table"+value.id+"' >";
                                content += "<thead>";
                                    content += "<tr>";
                                        content += "<th>No</th>";
                                        content += "<th>หมายเลขพัสดุ</th>";
                                        content += "<th>รายละเอียดผู้รับ</th>";
                                        content += "<th>สถานะงาน</th>";
                                        content += "<th >"+thfour+"</th>";
                                    content += "</tr>";
                                content += "</thead>";
                                content += "<tbody>";
                                content += "</tbody>";
                            content += "</table>";
                        content += "</div>";

                    });
                    Swal.fire({
                        customClass: 'swal-wide',
                        showCancelButton: false,
                        showConfirmButton: false,
                        reverseButtons: false,
                        html:   '<div class="row" style="margin-left: -58px !important; margin-right: -58px !important; margin-top: -15px !important;">'+
                                    '<div class="col-lg-12 col-md-12">'+
                                        '<div class="card">'+
                                            '<div class="card-header" style="color:#fff; background-color:'+bg+';">'+type+'</div>'+
                                            '<div class="card-body text-left" id="result_body_table" style="font-size:14px;">'+
                                                content+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'
                    });
                    $.each(DropCenters, function(index, value){
                        if(type == 'COD'){
                            $(function () {
                                var table = $('.data-table'+value.id).DataTable({
                                    processing: true,
                                    serverSide: true,
                                    ajax: {
                                        method:"POST",
                                        url:"{{url('track_Detail_from_result')}}",
                                        dataType: 'json',
                                        data:{
                                                "_token": "{{ csrf_token() }}",
                                                "type": type,
                                                "droup_id": value.id
                                            },
                                    },
                                    columns: [
                                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                                        {data: 'tracking_no', name: 'tracking_no'},
                                        {data: 'senderName', name: 'senderName'},
                                        {data: 'tracking_status', name: 'tracking_status'},
                                        {data: 'created_at', name: 'created_at', className:"text-right"}
                                    ]
                                });
                                var table = $('.data-table'+value.id).DataTable();
                                $('.data-table'+value.id).on('draw.dt', function () {
                                    if (table.data().any() == false){
                                        // $('#title_id_'+value.id).css("margin-top","0px");
                                        $('#showdatatable'+value.id).css("display","none");
                                        countEmpty += 1;
                                    }
                                    if(countEmpty == DropCenters.length){
                                        $("#result_body_table").html(
                                            '<div align="center" style="padding:10px 15px;">'+
                                            'No data available in table'+
                                            '</div>'
                                        );
                                    }
                                });
                            });
                        }else{
                            $(function () {
                                var table = $('.data-table'+value.id).DataTable({
                                    processing: true,
                                    serverSide: true,
                                    ajax: {
                                        method:"POST",
                                        url:"{{url('track_Detail_from_result')}}",
                                        dataType: 'json',
                                        data:{
                                                "_token": "{{ csrf_token() }}",
                                                "type": type,
                                                "droup_id": value.id
                                            },
                                    },
                                    columns: [
                                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                                        {data: 'tracking_no', name: 'tracking_no'},
                                        {data: 'senderName', name: 'senderName'},
                                        {data: 'tracking_status', name: 'tracking_status'},
                                        {data: 'created_at', name: 'created_at'}
                                    ]
                                });
                                var table = $('.data-table'+value.id).DataTable();
                                $('.data-table'+value.id).on('draw.dt', function () {
                                    if (table.data().any() == false){
                                        $('#showdatatable'+value.id).css("display","none");
                                        countEmpty += 1;
                                        // $('#title_id_'+value.id).css("margin-top","px");
                                        // console.log($('#title_id_'+value.id).html());
                                    }
                                    if(countEmpty == DropCenters.length){
                                        $("#result_body_table").html(
                                            '<div align="center" style="padding:10px 15px;">'+
                                            'No data available in table'+
                                            '</div>'
                                        );
                                    }
                                });
                            });
                        }
                    });
            @endif
        }
    }

    function note_rtn_status(id){
        Swal.fire({
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<div class="row" style="margin-left: -58px !important; margin-right: -58px !important; margin-top: -15px !important;">'+
                        '<div class="col-lg-12 col-md-12">'+
                            '<div class="card">'+
                                '<div class="card-header bg-danger" style="color:#fff;">แจ้งปัญหา</div>'+
                                '<div class="card-body text-left" style="font-size:14px;">'+
                                    '<form action="/setclearday_status" method="POST">'+
                                        '{{csrf_field()}}'+
                                        '<input type="hidden" name="tracking_id" id="tracking_id" value="'+id+'" />'+
                                        '<textarea name="note" id="note" rows="4" class="form-control" placeholder="กรอกเหตุผลที่ไม่สามารถจ่ายคืนได้" required></textarea>'+
                                        '<div align="right" style="padding-top: 10px;">'+
                                            '<button class="btn btn-success">ยืนยันการแจ้งปัญหา</button>'+
                                        '</div>'+
                                    '</form>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'
        });
    }

    dvl_courier_driver_list();
    setInterval(() => {
        dvl_courier_driver_list();
    }, 600000);
    function dvl_courier_driver_list(){
        content = "<table class='table data-table-dvl' >";
            content += "<thead>";
                content += "<tr>";
                    content += "<th>No</th>";
                    content += "<th>ผู้นำส่ง</th>";
                    content += "<th>CON</th>";
                    content += "<th>POD</th>";
                    content += "<th>DLY</th>";
                    content += "<th>COD</th>";
                content += "</tr>";
            content += "</thead>";
            content += "<tbody>";
            content += "</tbody>";
        content += "</table>";
        $("#courierDVLlist").html(content);
        $(function () {
            var table = $('.data-table-dvl').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    method:"POST",
                    url:"{{url('dvl_courier_driver_list')}}",
                    dataType: 'json',
                    data:{
                            "_token": "{{ csrf_token() }}"
                        },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'emp_firstname', name: 'emp_firstname'},
                    {data: 'CONS', name: 'CONS', className:"text-center"},
                    {data: 'POD', name: 'POD', className:"text-center"},
                    {data: 'DLY', name: 'DLY', className:"text-center"},
                    {data: 'all_cod', name: 'all_cod', className:"text-right"}
                ]
            });
        });
    }

    var v_t_id = "";
    function add_tracking_note(t_id, e_id, t_note){
        if(v_t_id !== ""){
            v_note_html = $("#note_content"+v_t_id).html();
            if(v_note_html == ""){
                $("#shownote"+v_t_id).css('display','none');
            }else{
                $("#shownote"+v_t_id).css('display','block');
            }
            $("#formnote"+v_t_id).html('');
        }
        v_t_id = t_id;
        note_html = $("#note_content"+t_id).html();
        var note = note_html.split('<br>')[0];
        content = '';
        content += '<form action="/Update_tracking_note" id="Update_tracking_note" method="POST">';
            content += '{{csrf_field()}}';
            content += '<input type="hidden" name="emplayee_id" id="emplayee_id" value="'+e_id+'" />';
            content += '<input type="hidden" name="tracking_id" id="tracking_id" value="'+t_id+'" />';
            content += '<input name="note" id="note" class="form-control" placeholder="เพิ่มโน๊ต" required value="'+note+'">';
            content += '<div align="right" style="padding-top: 10px;">';
                content += '<span id="shownote_data"></span><button type="submit" class="btn btn-success">บันทึก</button>';
                content += '&nbsp;<button type="buttton" class="btn btn-light" onclick="cancel_tracking_note(\''+t_id+'\')">ยกเลิก</button>';
            content += '</div>';
        content += '</form>';
        $("#shownote"+t_id).css('display','none');
        $("#formnote"+t_id).html(content);

        $("form#Update_tracking_note").submit(function(){
            var formData = new FormData(this);
            $.ajax({
                url: '{{url('Update_tracking_note')}}',
                type: 'POST',
                data: formData,
                async: false,
                success: function(data){
                    result = JSON.parse(data);
                    if(result.status == '1'){
                        $("#shownote"+v_t_id).css('display','block');
                        $("#formnote"+v_t_id).html('');
                        $("#note_content"+v_t_id).html(result.msg);
                    }else{
                        $("#shownote_data").html("เกิดข้อผิดตลาด");
                        setTimeout(() => {
                            $("#shownote_data").html("");
                        }, 2000);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
            return false;
        });
    }

    function cancel_tracking_note(t_id){
        v_t_id = "";
        note_html = $("#note_content"+t_id).html();
        if(note_html == ""){
            $("#shownote"+t_id).css('display','none');
        }else{
            $("#shownote"+t_id).css('display','block');
        }
        $("#formnote"+t_id).html('');
    }
</script>
@endsection