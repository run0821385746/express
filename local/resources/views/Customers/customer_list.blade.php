@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">ข้อมูลลูกค้า
        </div>
        <div class="card-body table-responsive">
            <table class="data-table align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>  
                    <tr>
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">รหัสลูกค้า</th>
                        <th class="text-left">ชื่อ</th>
                        <th class="text-left">ที่อยู่</th>
                        <th class="text-center">รหัสไปรษณีย์</th>
                        <th class="text-center">เบอร์ติดต่อ</th>
                        <th class="text-right">สถานะ</th>
                        <th class="text-right">บัญชีCOD</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>  
                    {{-- @if (!empty($customers))
                        @foreach($customers as $customer)
                            <tr>
                            <td class="text-center text-muted">{{$customer->id}}</td>
                                <td class="text-left">{{$customer->cust_name}}</td>
                                <td class="text-left">{{$customer->cust_address}} {{$customer->District['name_th'].' '.$customer->amphure['name_th'].' '.$customer->province['name_th']}}</td>
                                <td class="text-center">{{$customer->cust_postcode}}</td>
                                <td class="text-center">{{$customer->cust_phone}}</td>
                                <td class="text-right">
                                        @if ($customer->cust_status == '1')
                                            ปกติ
                                        @else
                                            ยกเลิก
                                        @endif
                                </td>
                                <td class="text-center">
                                    <a href="/get_customer_detail_for_edit/{{$customer->id}}">
                                        <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm">แก้ไขข้อมูล</button>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif --}}
                </tbody>
            </table>
            <br><br>
        </div>
        <div class="d-block text-left card-footer">
            <div class="row">
           <!--      <div class="col-lg-6 col-md-6 text-left">
                    <nav class="" aria-label="Page navigation example">
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
                    </nav>
                </div> -->
                <div class="col-lg-6 col-md-6 text-right"></div>
                <div class="col-lg-6 col-md-6 text-right">
                    <a href="/customer_management_add"><button class="btn-wide  btn btn-primary">เพิ่มลูกค้าใหม่</button></a>
                    <button class="btn-wide  btn btn-success">Export Documents</button>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    $(function () {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                method:"POST",
                url:"{{url('customerListDataTable')}}",
                dataType: 'json',
                data:{
                        "_token": "{{ csrf_token() }}"
                    },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'id', name: 'id'},
                {data: 'cust_name', name: 'cust_name'},
                {data: 'cust_address', name: 'cust_address', className:'text-left'},
                {data: 'cust_postcode', name: 'cust_postcode', className:'text-center'},
                {data: 'cust_phone', name: 'cust_phone', className:'text-center'},
                {data: 'cust_status', name: 'cust_status'},
                {data: 'cust_cod_register_status', name: 'cust_cod_register_status', className:'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });

    function addCustomerCOD(cutId, phone, codid){
        var token = '{{ csrf_token()}}';
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
            html:   '<form action="{{url('addCustomerCOD')}}" method="post" enctype="multipart/form-data">'+
                    '{{csrf_field()}}'+
                    '<input type="hidden" name="_token" value="'+token+'">'+
                    '<input type="hidden" name="id" value="'+cutId+'">'+
                    '<input type="hidden" name="codid" value="'+codid+'">'+
                    '<input type="hidden" name="ManagementMenu" value="all">'+
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
</script>