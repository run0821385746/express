@extends("input")
@section("content")

<div class="col-lg-12 col-md-12">
    <div class="main-card  card">
        <div class="card-header">
            <h4>ข้อมูลลูกค้า ค้นหาด้วย {{!empty($search_phone) ? $search_phone : null}}</h4>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">รหัสลูกค้า</th>
                        <th width="20%" class="text-left">ชื่อลูกค้า</th>
                        <th width="28%" class="text-left">ที่อยู่ลูกค้า</th>
                        <th width="8%" class="text-left">รหัสไปรษณีย์</th>
                        <th width="10%" class="text-left">เบอร์ติดต่อ</th>
                        <th width="4%" class="text-left">บัญชี COD</th>
                        <th width="20%" class="text-center">ทำรายการ</th>
                    </tr>    
                </thead>
                <tbody>  
                    @if (!empty($customers))
                        @if (count($customers)==0)
                        <tr>
                            <td colspan="6" class="text-center"><font color="red">ขออภัย!..ไม่พบข้อมูลที่ค้นหา กรุณากดเพิ่มข้อมูลลูกค้าใหม่ก่อน</font></td>
                        </tr>
                        @else
                            @foreach($customers as $customer)
                            <tr>
                                <td class="text-center text-muted">{{$customer->id}}</td>
                                <td class="text-left">{{$customer->cust_name}}</td>
                                <td class="text-left"> {{$customer->cust_address}} {{$customer->District['name_th'].' '.$customer->amphure['name_th'].' '.$customer->province['name_th']}}</td>
                                <td class="text-left">{{$customer->cust_postcode}}</td>
                                <td class="text-left">{{$customer->cust_phone}}</td>
                                <td class="text-left">
                                    @if ($customer->cust_cod_register_status != null)
                                        <button type="button" onclick="addCustomerCOD('{{$customer->id}}','{{$customer->cust_phone}}','{{$customer->cust_cod_register_status}}')" class="btn btn-outline-success" style="padding: 1px 2px; border:none;">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                    @else
                                        <button type="button" onclick="addCustomerCOD('{{$customer->id}}','{{$customer->cust_phone}}','')" class="btn btn-outline-secondary" style="padding: 1px 2px; border:none;">
                                            <i class="fa fa-plus-circle" aria-hidden="true" title="Copy to use minus-circle"></i>
                                        </button>
                                    @endif
                                
                                </td>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 text-center">
                                            <form action="/booking" method="POST"
                                                @if (!empty($booking_id)) 
                                                    @php
                                                        $Booking = App\Model\Booking::find($booking_id); 
                                                    @endphp 
                                                    @if (!empty($Booking->booking_type == "2" && $Booking->booking_status == 'request'))
                                                        onsubmit="return confirm('ต้องการยกเลิกรายการเรียกรถเข้ารับพัสดุหรือไม่ ?');"
                                                    @endif
                                                @endif
                                            >
                                                {{csrf_field()}}
                                                <input type="hidden" name="branch_id" value="{{ $user->emp_branch_id }}">
                                                <input type="hidden" name="booking_id" value="{{!empty($booking_id) ? $booking_id : null}}">
                                                <input type="hidden" name="booking_type" value="1">
                                                <input type="hidden" name="customer_id" value="{{$customer->id}}">
                                                @if (!empty($booking_id))
                                                    @php
                                                        if ($Booking->booking_type == "2" && $Booking->booking_status == 'request') {
                                                            echo '<button type="submit" class="btn btn-block btn-danger btn-sm">รับพัสดุหน้าร้าน</button>';
                                                        }else {
                                                            echo '<button type="submit" class="btn btn-block btn-danger btn-sm">รับพัสดุหน้าร้าน</button>';
                                                        }
                                                    @endphp
                                                @else
                                                    <button type="submit" class="btn btn-block btn-danger btn-sm">รับพัสดุหน้าร้าน</button>
                                                @endif
                                            </form>
                                            {{-- {{dd($user)}} --}}
                                        </div>
                                        <div class="col-lg-6 col-md-6 text-center">
                                            <form action="/booking" method="POST">
                                                {{csrf_field()}}
                                                <input type="hidden" name="branch_id" value="{{ $user->emp_branch_id }}">
                                                <input type="hidden" name="booking_id" value="{{!empty($booking_id) ? $booking_id : null}}">
                                                <input type="hidden" name="booking_type" value="2">
                                                <input type="hidden" name="customer_id" value="{{$customer->id}}">
                                                <button type="submit" class="btn btn-block btn-warning btn-sm">เรียกรถรับพัสดุ </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>  
                            </tr>
                            @endforeach
                        @endif
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-block text-left card-footer">
            <button id="button" type="button" onclick="addCustomer();" class="btn btn-primary ">เพิ่มลูกค้าใหม่</button>
            @if (!empty($booking_id))
            <a href="/connectBooking/{{!empty($booking_id) ? $booking_id : null}}"><button class="btn-wide  btn btn-light ">กลับ</button></a>
            @else
                <a href="/input"><button class="btn-wide  btn btn-light ">กลับ</button></a>
            @endif
        </div>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token()}}">
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
    function addCustomer(){
        var token = $('#token').val();
        Swal.fire({
            type: 'warning',
            title: 'เพิ่มข้อมูลลูกค้า',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,  
            html:   '<form action="/addNewSenderCustomer" method="post">'+
                    '{{csrf_field()}}'+
                    '<input type="hidden" name="_token" value="'+token+'">'+
                    '<div class="position-relative form-group text-left">'+
                        '<label for="cust_name" class="">ชื่อลูกค้า</label>'+
                        '<input name="cust_name" id="cust_name" placeholder="" type="text" class="form-control" required>'+
                    '</div>'+
                    '<div class="position-relative form-group text-left">'+
                        '<label for="exampleText" class="">ที่อยู่ </label>'+
                        '<input name="cust_address" id="exampleText" class="form-control">'+
                    '</div>'+
                    '<div class="row">'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<label for="cust_postcode" class="">รหัสไปรษณีย์</label>'+
                            '<div class="input-group mb-3">'+
                                '<input name="cust_postcode" id="cust_postcode" placeholder="" type="text" class="form-control" required>'+
                                '<div class="input-group-append">'+
                                    '<button class="btn btn-info" type="button" onclick="findaddress()">ค้นหา</button>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="cust_province" class="">จังหวัด</label>'+
                                '<select name="cust_province" id="cust_province" class="form-control" required onchange="findamphure(this)">'+
                                    '<option value="">เลือกจังหวัด</option>'+
                                    @foreach ($provinces as $province)
                                        '<option value="{{$province->id}}">{{$province->name_th}}</option>'+
                                    @endforeach
                                '</select>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+ 
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="cust_district" class="">อำเภอ</label>'+
                                '<select name="cust_district" id="cust_district" class="form-control" required onchange="finddistric(this)">'+
                                    '<option value="">เลือกอำเภอ</option>'+
                                '</select>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="cust_sub_district" class="">ตำบล</label>'+
                                '<select name="cust_sub_district" id="cust_sub_district" class="form-control" required onchange="findzipcode(this)">'+
                                    '<option value="">เลือกตำบล</option>'+
                                '</select>'+
                           '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="cust_phone" class="">เบอร์โทร</label>'+
                                '<input name="cust_phone" id="cust_phone" placeholder="" type="text" class="form-control" value="{{!empty($search_phone) ? $search_phone : null}}" required>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+
                        '<div class="col-lg-12 col-md-12">'+
                            '<button type="submit" class="mt-1 btn btn-primary">บันทึกลูกค้าใหม่</button>'+
                        '</div>'+
                    '</div>'+
                '</form>'
        });
    }
    
    function addCustomerCOD(cutId, phone, codid){
        var token = $('#token').val();
        formtitle = "สมัคร";
        saventm = "สร้างบัญชี COD";
        if(codid != ""){
            formtitle = "แก้ไข";
            saventm = "บันทึก";
        }
        Swal.fire({
            type: 'warning',
            title: formtitle+'บัญชี COD',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,  
            html:   '<form action="/addCustomerCOD" method="post" enctype="multipart/form-data">'+
                    '{{csrf_field()}}'+
                    '<input type="hidden" name="_token" value="'+token+'">'+
                    '<input type="hidden" name="id" value="'+cutId+'">'+
                    '<input type="hidden" name="codid" value="'+codid+'">'+
                    '<div class="row">'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<label for="cust_phone" class="">เบอร์มือถือ</label>'+
                            '<div class="input-group mb-3">'+
                                '<input name="cust_phone" id="cust_phone" placeholder="" type="text" class="form-control" value="'+phone+'" maxlength="10" required readonly>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="cust_mail" class="">E-Mail</label>'+
                                '<input name="cust_mail" id="cust_mail" placeholder="" type="email" class="form-control" required>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<label for="cust_bookbank_name" class="">ชื่อบัญชีธนาคาร</label>'+
                            '<div class="input-group mb-3">'+
                                '<input name="cust_bookbank_name" id="cust_bookbank_name" placeholder="" type="text" class="form-control" required>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="cust_id_card" class="">เลข บัตรประชาชน/พาสปอร์ต</label>'+
                                '<input name="cust_id_card" id="cust_id_card" placeholder="" type="text" class="form-control" required>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<label for="cust_bank_no" class="">หมายเลขบัญชีธนาคาร</label>'+
                            '<div class="input-group mb-3">'+
                                '<input name="cust_bank_no" id="cust_bank_no" placeholder="" type="text" class="form-control" maxlength="10" required>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="cust_bank_name" class="">ธนาคาร</label>'+
                                '<select name="cust_bank_name" id="cust_bank_name" class="form-control" required>'+
                                    '<option value="ธนาคารกรุงเทพ" id="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>'+
                                    '<option value="ธนาคารกรุงไทย" id="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>'+
                                    '<option value="ธนาคารกรุงศรีอยุธยา" id="ธนาคารกรุงศรีอยุธยา">ธนาคารกรุงศรีอยุธยา</option>'+
                                    '<option value="ธนาคารกสิกรไทย" id="ธนาคารกสิกรไทย">ธนาคารกสิกรไทย</option>'+
                                    '<option value="ธนาคารเกียรตินาคิน" id="ธนาคารเกียรตินาคิน">ธนาคารเกียรตินาคิน</option>'+
                                    '<option value="ธนาคารซีไอเอ็มบี" id="ธนาคารซีไอเอ็มบี">ธนาคารซีไอเอ็มบี</option>'+
                                    '<option value="ธนาคารทหารไทย" id="ธนาคารทหารไทย">ธนาคารทหารไทย</option>'+
                                    '<option value="ธนาคารทิสโก้" id="ธนาคารทิสโก้">ธนาคารทิสโก้</option>'+
                                    '<option value="ธนาคารไทยพาณิชย์" id="ธนาคารไทยพาณิชย์">ธนาคารไทยพาณิชย์</option>'+
                                    '<option value="ธนาคารธนชาต" id="ธนาคารธนชาต">ธนาคารธนชาต</option>'+
                                    '<option value="ธนาคารยูโอบี" id="ธนาคารยูโอบี">ธนาคารยูโอบี</option>'+
                                    '<option value="ธนาคารแลนด์" id="ธนาคารแลนด์">ธนาคารแลนด์</option>'+
                                    '<option value="ธนาคารสแตนดาร์ดชาร์เตอร์ด" id="ธนาคารสแตนดาร์ดชาร์เตอร์ด">ธนาคารสแตนดาร์ดชาร์เตอร์ด</option>'+
                                    '<option value="ธนาคารออมสิน" id="ธนาคารออมสิน">ธนาคารออมสิน</option>'+
                                    '<option value="ธนาคารไอซีบีซี" id="ธนาคารไอซีบีซี">ธนาคารไอซีบีซี</option>'+
                                '</select>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+ 
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="cust_billing_address" class="">ที่อยู่สำหรับเรียกเก็บเงิน</label>'+
                                '<input name="cust_billing_address" id="cust_billing_address" placeholder="" type="text" class="form-control" required>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="findAddressCOD" class="">เขตที่อยู่</label></br>'+
                                '<select class="js-data-example-ajax form-control" name="findAddressCOD" id="findAddressCOD" style="width:100% !importent; padding:30px 15px !importent;" required>'+
                                    
                                '</select>'+
                           '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+
                        '<div class="col-lg-12 col-md-12 text-left">'+
                        '<hr>'+
                        '</div>'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<label for="cust_idcard_front_img" style="font-size:12px; color:red;">รูปด้านหน้า บัตรประชาชน/หนังสือเดินทาง</label>'+
                            '</br><span id="cust_idcard_front_img_edit"></span>'+
                            '<div class="input-group mb-3">'+
                                '<input name="cust_idcard_front_img" id="cust_idcard_front_img" placeholder="" type="file" class="form-control" maxlength="10" required>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="cust_idcard_back_img" style="font-size:12px; color:red;">รูป ด้านหลังบัตรประชาชน/วีซ่าไทย</label>'+
                                '</br><span id="cust_idcard_back_img_edit"></span>'+
                                '<input name="cust_idcard_back_img" id="cust_idcard_back_img" placeholder="" type="file" class="form-control" required>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row"><hr>'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<label for="cust_bookbank_img" style="font-size:12px; color:red;">รูปหน้าแรกสมุดบัญชีธนาคาร</label>'+
                            '</br><span id="cust_bookbank_img_edit"></span>'+
                            '<div class="input-group mb-3">'+
                                '<input name="cust_bookbank_img" id="cust_bookbank_img" placeholder="" type="file" class="form-control" maxlength="10" required>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-lg-6 col-md-6 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="cust_sign_contract_img" style="font-size:12px; color:red;">รูปถ่ายเอกสารสัญญา</label>'+
                                '</br><span id="cust_sign_contract_img_edit"></span>'+
                                '<input name="cust_sign_contract_img" id="cust_sign_contract_img" placeholder="" type="file" class="form-control" required>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+
                        '<div class="col-lg-12 col-md-12">'+
                            '<button type="submit" class="mt-1 btn btn-primary">'+saventm+'</button>'+
                        '</div>'+
                    '</div>'+
                '</form>'
        });
        $('.js-data-example-ajax').select2({
            tags: [],
            ajax: {
                url: '{{url('find_areafromzipcode')}}',
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (term, _token) {
                    return {
                        term: term.term,
                        _token: "{{ csrf_token() }}"
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.tname+'-'+item.aname+'-'+item.pname+'-'+item.zip_code,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });

        $(".select2-selection--single").click(function(){
            $('.select2-dropdown').css("z-index", "9999");
        });

        if(codid != ""){
            $.ajax({
                method:"POST",
                url:"{{url('cod_account_detail')}}",
                dataType: 'json',
                data:{"codid":codid, "_token": "{{ csrf_token() }}",},
                success:function(data){
                    console.log(data);
                    $("#cust_mail").val(data[0].cust_mail);
                    $("#cust_bookbank_name").val(data[0].cust_bookbank_name);
                    $("#cust_id_card").val(data[0].cust_id_card);
                    $("#cust_bank_no").val(data[0].cust_bank_no);
                    $("#"+data[0].cust_bank_name).attr('selected', 'selected');
                    $("#cust_billing_address").val(data[0].cust_billing_address); 2
                    $("#findAddressCOD").val(data[0].findAddressCOD);
                    $('#findAddressCOD').append($("<option></option>").attr("value", data[0].district.id).text(data[0].district.name_th+'-'+data[0].amphure.name_th+'-'+data[0].province.name_th+'-'+data[0].district.zip_code)); 
                    $("#cust_idcard_front_img_edit").html('<a href="{{url("")}}/local/public/uploadimg/cod_account/'+cutId+'/'+data[0].cust_idcard_front_img+'" target="_blank" style="font-size:10px; color:blue;">view</a>');
                    $("#cust_idcard_back_img_edit").html('<a href="{{url("")}}/local/public/uploadimg/cod_account/'+cutId+'/'+data[0].cust_idcard_back_img+'" target="_blank" style="font-size:10px; color:blue;">view5</a>');
                    $("#cust_bookbank_img_edit").html('<a href="{{url("")}}/local/public/uploadimg/cod_account/'+cutId+'/'+data[0].cust_bookbank_img+'" target="_blank" style="font-size:10px; color:blue;">view</a>');
                    $("#cust_sign_contract_img_edit").html('<a href="{{url("")}}/local/public/uploadimg/cod_account/'+cutId+'/'+data[0].cust_sign_contract_img+'" target="_blank" style="font-size:10px; color:blue;">view</a>');
                    $("#cust_idcard_front_img").attr('required', false);
                    $("#cust_idcard_back_img").attr('required', false);
                    $("#cust_bookbank_img").attr('required', false);
                    $("#cust_sign_contract_img").attr('required', false);
                }
            });
        }
    }

    // function getareafromzipcode(zipcode){
    //     zipcode = zipcode.value;
    //     length = zipcode.length;
    //     if(length == 5){
    //         $.ajax({
    //             method:"POST",
    //             url:"{{url('find_areafromzipcode')}}",
    //             dataType: 'json',
    //             data:{"zipcode":zipcode, "_token": "{{ csrf_token() }}",},
    //             success:function(data){
    //                 $('#findAddressCOD').html('');
    //                 $.each(data, function(i, item) {
    //                     $('#findAddressCOD').append($("<option></option>").attr("value", item.id).text(item.tname+'-'+item.aname+'-'+item.pname+'-'+item.zip_code)); 
    //                 });
    //             }
    //         });
    //     }
    // }

    function findaddress(){
        zipcode = $("#cust_postcode").val();
        if(zipcode != ""){
            $.ajax({
                method:"POST",
                url:"{{url('findaddress')}}",
                dataType: 'json',
                data:{"zipcode":zipcode, "_token": "{{ csrf_token() }}",},
                success:function(data){
                    // console.log(data.province[0].name_th);
                    $('#cust_province').html('');
                    $('#cust_province').append($("<option></option>").attr("value", '').text('เลือกจังหวัด')); 
                    $.each(data.provincesall, function(i, item) {
                        if(data.province[0].id != item.id){
                            $('#cust_province').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
                        }else{
                            $('#cust_province').append($("<option></option>").attr("value", item.id).attr("selected","selected").text(item.name_th)); 
                        }
                    });

                    $('#cust_district').html('');
                    $.each(data.amphures, function(i, item) {
                        $('#cust_district').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
                    });

                    $('#cust_sub_district').html('');
                    $.each(data.Districts, function(i, item) {
                        $('#cust_sub_district').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
                    });
                }
            });
        }
    }

        function findamphure(provincename){
            provincename = provincename.value;
            $.ajax({
                method:"POST",
                url:"{{url('find_amphure')}}",
                dataType: 'json',
                data:{"provinceid":provincename, "_token": "{{ csrf_token() }}",},
                success:function(data){
                    $('#cust_district').html('');
                    $('#cust_district').append($("<option></option>").attr("value", '').text('เลือกอำเภอ')); 
                    $('#cust_district').attr('readonly', false);
                    $.each(data, function(i, item) {
                        $('#cust_district').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
                    });
                }
            });
        }
        
        function finddistric(districname){
            districname = districname.value;
            $.ajax({
                method:"POST",
                url:"{{url('finddistric')}}",
                dataType: 'json',
                data:{"districid":districname, "_token": "{{ csrf_token() }}",},
                success:function(data){
                    $('#cust_sub_district').html('');
                    $('#cust_sub_district').append($("<option></option>").attr("value", '').text('กรุณาเลือกตำบล')); 
                    $('#cust_sub_district').attr('readonly', false);
                    $.each(data, function(i, item) {
                        $('#cust_sub_district').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
                    });
                }
            });
        }
        
        function findzipcode(zipcode){
            districename = zipcode.value;
            $.ajax({
                method:"POST",
                url:"{{url('findzipcode')}}",
                dataType: 'json',
                data:{"districeid":districename, "_token": "{{ csrf_token() }}",},
                success:function(data){
                    $('#cust_postcode').attr('readonly', false);
                    $("#cust_postcode").val(data.zip_code);
                }
            });
        }

        $('#button').click(function() {
            $('#new_customer').toggle();
        })
</script>

@endsection