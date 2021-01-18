@extends('input')
@section('content')
  
<div class="col-lg-12 col-md-12">
    <div class="main-card  card">
        <div class="card-header">
        <h4>ข้อมูลผู้รับ ค้นหาด้วย : {{!empty($search_phone)? $search_phone : null}}</h4>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">รหัสผู้รับ</th>
                        <th width="20%" class="text-left">ชื่อผู้รับ</th>
                        <th width="40%" class="text-left">ที่อยู่ผู้รับ</th>
                        <th width="10%" class="text-left">รหัสไปรษณีย์</th>
                        <th width="10%" class="text-left">เบอร์ติดต่อ</th>
                        <th width="10%" class="text-center">ทำรายการ</th>
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
                                <td class="text-center">
                                    <form action="/updateReceivingTracking/{{$customer->id}}" method="POST"> 
                                        {{csrf_field()}}
                                        @method('PUT')
                                        <input type="hidden" name="tracking_id" id="tracking_id" value="{{$tracking_id}}">
                                        <input type="hidden" name="customer_id" value="{{$customer->id}}">
                                        <button type="submit" class="btn btn-warning btn-sm">เลือก </button>
                                    </form>
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
        <a href="/getTrackingDetailFormTrackingId/{{$tracking_id}}"><button class="btn-wide  btn btn-light ">กลับ</button></a>
        </div>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token()}}">
    </div>
</div>

<script>
    function addCustomer(){
        var token = $('#token').val();
        var tracking_id = '{{$tracking_id}}';
        Swal.fire({
            type: 'warning',
            title: 'เพิ่มข้อมูลลูกค้า',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,    
            html:   '<form action="/addNewReceiveCustomer" method="post">'+
                    '{{csrf_field()}}'+
                    '<input type="hidden" name="tracking_id" value="'+tracking_id+'">'+
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