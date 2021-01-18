@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="row">
            <div class="col-lg-5">
                <div class="card-header">ค้นหาด้วย Tracking</div>
                <div class="col-lg-7">
                    <div class="app-header__content">
                        <div class="app-header-left">
                            <div class="search-wrapper" id="addclass_open">
                                <div class="input-holder">
                                    <input type="text" class="search-input" placeholder="Type to search" id="tracking_no" />
                                    <button class="search-icon" id="find_track_care" onclick="find_paarcel_care()"><span></span></button>
                                </div>
                                <button onclick="closeinput()" class="close"></button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12" id="parcel_care_datail">
    <div class="main-card mb-3 card">
        <div class="card-header">
            ข้อมูลพัสดุรับ
            <span style="position: absolute; right:15px;" id="show_cancle"></span>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">วันที่รับพัสดุ</th>
                        <th>ข้อมูลผู้ส่ง</th>
                        <th>ข้อมูลผู้รับ</th>
                        <th class="text-center">จำนวนชิ้น</th>
                        <th class="text-right">ค่าส่งพัสดุ/บาท</th>
                        <th class="text-right">COD/บาท</th>
                        <th class="text-center">สถานะ</th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center text-muted" id="recive_date"></td>
                        <td>
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left flex2">
                                        <div class="widget-heading" id="sender"></div>
                                        <div class="widget-subheading opacity-7"></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left flex2">
                                        <div class="widget-heading" id="reciver"></div>
                                        <div class="widget-subheading opacity-7"></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center" id="parcel_amount"></td>
                        <td class="text-right" id="track_cost"></td>
                        <td class="text-right" id="cod_amount"></td>
                        <td class="text-center" id="status"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-muted" id="tracking_note"></td>
                        <td colspan="5" class="text-muted"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-header">
                ข้อมูลพัสดุรับ
            </div>
            <div class="table-responsive">
                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th width="20%" class="text-left">วันเวลา</th>
                            <th width="40%" class="text-left">ประวัติการนำส่ง</th>
                            <th width="30%" class="text-left">พนักงาน</th>
                            <th width="30%" class="text-left">สาขา</th>
                            <th width="10%" class="text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody id="showHostory">
                        {{-- <tr>
                            <td></td>
                            <td class="text-left text-muted">11-03-2020 12:34</td>
                            <td class="text-left">เบิกจ่าย courier</td>
                            <td class="text-left">นิรันดร์ หยาง</td>
                            <td class="text-center">
                                <div class="badge badge-success">Completed</div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-left text-muted">11-03-2020 12:34</td>
                            <td class="text-left">เบิกจ่าย courier</td>
                            <td class="text-left">นิรันดร์ หยาง</td>
                            <td class="text-center">
                                <div class="badge badge-success">Completed</div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-left text-muted">11-03-2020 12:34</td>
                            <td class="text-left">เบิกจ่าย courier</td>
                            <td class="text-left">นิรันดร์ หยาง</td>
                            <td class="text-center">
                                <div class="badge badge-success">Completed</div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-left text-muted">11-03-2020 12:34</td>
                            <td class="text-left">เบิกจ่าย courier</td>
                            <td class="text-left">นิรันดร์ หยาง</td>
                            <td class="text-center">
                                <div class="badge badge-success">Completed</div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-left text-muted">11-03-2020 12:34</td>
                            <td class="text-left">เบิกจ่าย courier</td>
                            <td class="text-left">นิรันดร์ หยาง</td>
                            <td class="text-center">
                                <div class="badge badge-success">Completed</div>
                            </td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>
<script>
    track_con = '<?= $track_con;?>';

    var input = document.getElementById("tracking_no");
    $("#parcel_care_datail").hide();
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("find_track_care").click();
        }
    });

    function find_paarcel_care(){
        if(input.value.length > 0){
            if(input.value.length >= 15 ){
                tracking_no = input.value;
                $.ajax({
                    method:"POST",
                    url:"{{url('find_paarcel_care')}}",
                    dataType: 'json',
                    data:{"tracking_no":tracking_no, "_token": "{{ csrf_token() }}"},
                    success:function(data){
                        if(data.recive_date != 'empty'){
                            $("#parcel_care_datail").show();
                            $("#recive_date").html(data.recive_date);
                            $("#sender").html(data.sender);
                            $("#reciver").html(data.reciver);
                            $("#parcel_amount").html(data.parcel_amount);
                            $("#track_cost").html(data.track_cost);
                            $("#cod_amount").html(data.cod_amount);
                            if(data.status == 'CustomerResiveDone' || data.status == 'CustomerResiveDoneReturn'){
                                $("#status").html('<div class="badge badge-success">จัดส่งสำเร็จ</div>');
                            }else if(data.status == 'Destroy'){
                                $("#status").html('<div class="badge badge-danger">รายการถูกยกเลิก</div>');
                            }else{
                                $("#status").html('<div class="badge badge-primary">กำลังดำเนินการ</div>');
                            }
                            
                            if(data.destroy_tracking == 'can_Destroy'){

                                $("#show_cancle").html('<button class="btn btn-danger btn-sm" onclick="confirm_destroy(\''+data.tracking_id+'\')">ลบรายการ Con</button>');

                            }else if(data.destroy_tracking == 'can_cancle_Destroy'){

                                $("#show_cancle").html('<button class="btn btn-warning btn-sm" onclick="confirm_cencle_destroy(\''+data.tracking_id+'\')">กู้คืนรายการ Con</button>');

                            }else if(data.destroy_tracking == 'can_not_cancle_Destroy'){

                                $("#show_cancle").html('<button class="btn btn-danger btn-sm" disabled>ลบรายการ Con</button>');

                            }

                            $("#tracking_note").html('<div style="background-color:#F9FFB0; border-radius:7px;">'+data.tracking_note+'<div>');

                            $.ajax({
                                method:"POST",
                                url:"{{url('find_paarcel_care_moveing')}}",
                                dataType: 'json',
                                data:{"tracking_no":tracking_no, "_token": "{{ csrf_token() }}"},
                                success:function(data){
                                    // alert(data);
                                    // console.log(data);
                                    // content = '';
                                    // $.each(data, function(i, item){
                                    //     date = moment(item.created_at).format("DD/MM/YYYY HH:mm")
                                    //     content += '<tr>';
                                    //         content += '<td>'+(i+1)+'</td>';
                                    //         content += '<td class="text-left text-muted">'+date+'</td>';
                                    //         content += '<td class="text-left">'+item.pacel_care_status.status+'</td>';
                                    //         content += '<td class="text-left">'+item.employee.emp_firstname+' '+item.employee.emp_lastname+'</td>';
                                    //         content += '<td class="text-center">';
                                    //             content += '<div class="badge badge-success">Completed</div>';
                                    //         content += '</td>';
                                    //     content += '</tr>';
                                    // })
                                    $("#showHostory").html(data);
                                }
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่พบ Tracking',
                                text: 'โปรดตรวจสอบ หมายเลข Tracking อีกครั้ง!'
                            });
                        }
                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Tracking ไม่ถูกต้อง',
                    text: 'โปรดตรวจสอบ หมายเลข Tracking อีกครั้ง!'
                });
            }
        }else{
            // alert("sss");
            input.focus();
        }
    }

    function confirm_destroy(id){
        Swal.fire({
            title: 'ยืนยันการลบ ?',
            text: "ต้องการ ลบ รายการ Con หรือไม่!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ยืนยัน'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{url('Destroy_tracking')}}/"+id;
            }
        })
    }
    
    function confirm_cencle_destroy(id){
        Swal.fire({
            title: 'ยืนยันการกู้คืน ?',
            text: "ต้องการ กู้คืน รายการ Con หรือไม่!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ยืนยัน'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{url('cancle_Destroy_tracking')}}/"+id;
            }
        })
    }

    function checkRTN(){
        valnew = input.value+'(RTN)';
        $("#tracking_no").val(valnew);
        document.getElementById("find_track_care").click();
    }

    function closeinput(){
        input.value = "";
    }
    
    var view_call_id = "";
    function view_c_call_table(id){
        // alert(id);
        if($("#c_call_table"+id).css("display") == "block"){
            $("#c_call_table"+id).css("display","none");
        }else{
            $("#c_call_table"+view_call_id).css("display","none");
            $("#c_call_table"+id).css("display","block");
            view_call_id = id;
        }
    }
    
    function status_9_detail(type, tracking_no){
        // alert(type);
        $.ajax({
            method:"POST",
            url:"{{url('tracking_success_detail')}}",
            dataType: 'json',
            data:{"tracking_no":tracking_no, "_token": "{{ csrf_token() }}", "Secure":"domefront470da840c0b3036974860d6dd04ddb8462eac72625c02a1fb7e369fa9806e8d8runback"},
            success:function(data){
                // console.log(data);
                if(type == '1'){
                    head = "จัดส่งสำเร็จ";

                    recive = '<b>รายละเอียดพัสดุ</b>';
                    recive += '<div>ชื่อผู้รับ : '+data.customer_name+'</div>';
                    recive += '<div>ที่อยู่ผู้รับ : '+data.customer_address+'</div>';
                    recive += '<div class="row">';
                        recive += '<div class="col-lg-6 col-md-6">';
                            recive += '<div>รหัสไปรษณีย์ : '+data.customer_postcode+'</div>';
                        recive += '</div>';
                        recive += '<div class="col-lg-6 col-md-6">';
                            recive += '<div>เบอร์มือถือ : '+data.customer_phone+'</div>';
                        recive += '</div>';
                    recive += '</div>';

                    signature = '<br>';
                    signature += '<img src="data:image/jpeg;base64,'+data.receive_signature+'" width="60%" />';
                }else if(type == '2'){
                    head = "ปลายทางรับสำเร็จ";

                    recive = '<b>รายละเอียดพัสดุ</b>';
                    recive += '<div>ชื่อผู้รับ : '+data.customer_name+'</div>';
                    recive += '<div>ที่อยู่ผู้รับ : '+data.customer_address+'</div>';
                    recive += '<div class="row">';
                        recive += '<div class="col-lg-6 col-md-6">';
                            recive += '<div>รหัสไปรษณีย์ : '+data.customer_postcode+'</div>';
                        recive += '</div>';
                        recive += '<div class="col-lg-6 col-md-6">';
                            recive += '<div>เบอร์มือถือ : '+data.customer_phone+'</div>';
                        recive += '</div>';
                    recive += '</div>';

                    signature = '';
                }else if(type == '3'){
                    head = "ส่งคืนผู้ส่งสำเร็จ";
                    recive = "<strong style='color:blue;'>ผู้ส่งรับคืนพัสดุตีกลับ</strong>";
                    signature = '';
                }
                Swal.fire({
                    showCancelButton: false,
                    showConfirmButton: false,
                    reverseButtons: false,
                    html:   '<div class="row" style="margin-left: -58px !important; margin-right: -58px !important; margin-top: -15px !important;">'+
                                '<div class="col-lg-12 col-md-12">'+
                                    '<div class="card">'+
                                        '<div class="card-header bg-success" style="color:#fff;">'+head+'</div>'+
                                        '<div class="card-body text-left" style="font-size:14px;"> Tracking No : '+tracking_no+'<br>'+recive+'<br>'+
                                            '<div class="card-body text-center">'+
                                                '<img src="data:image/jpeg;base64,'+data.receive_photo+'" width="60%" />'+signature+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                });
            }
        });
    }

    if(track_con !== ''){
        $("#tracking_no").val(track_con);
        $("#addclass_open").addClass("active");
        document.getElementById("find_track_care").click();
    }
    
</script>
@endsection