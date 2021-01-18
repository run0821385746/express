@extends('input')
@section('content')

<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
  
            @if (!empty($customer->id))
                <h5 class="card-title">แก้ไขข้อมูลลูกค้า</h5>
                <form action="/customer/{{$customer->id}}" method="post">
                @method('PUT')
            @else  
                <h5 class="card-title">เพิ่มลูกค้าใหม่</h5>
                <form action="/customer" method="post">
            @endif

            {{csrf_field()}}
            <div class="row">  
                    <div class="col-lg-12 col-md-12">
                        <div class="position-relative form-group">
                            <label for="cust_name" class="">ชื่อ</label>
                            <input name="cust_name" id="cust_name" placeholder="" type="text"
                                class="form-control" value="{{!empty($customer->cust_name) ? $customer->cust_name : null}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="position-relative form-group">
                            <label for="cust_address" class="">ที่อยู่ติดต่อ</label>
                            <textarea name="cust_address" id="cust_address" placeholder="" type="text"
                                class="form-control">{{!empty($customer->cust_address) ? $customer->cust_address : null}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        {{csrf_field()}}
                        <div class="position-relative form-group">
                            <label for="cust_postcode" class="">รหัสไปรษณีย์</label>
                            {{-- <div class="input-group">
                                <input type="text" class="form-control" name="cust_postcode" id="cust_postcode" value="{{!empty($customer->cust_postcode) ? $customer->cust_postcode : null}}" readonly>
                                <a href="postcode_get_list">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">ค้นหา</button>
                                    </div>
                                </a>
                            </div> --}}
                            <div class="input-group mb-3">
                                <input name="cust_postcode" id="cust_postcode" value="{{!empty($customer->cust_postcode) ? $customer->cust_postcode : null}}" type="text" class="form-control" required>
                                <div class="input-group-append">
                                    <button class="btn btn-info" type="button" onclick="findaddress()">ค้นหา</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="cust_province" class="">จังหวัด</label>
                            <select name="cust_province" id="cust_province" class="form-control" required onchange="findamphure(this)">
                                <option value="">เลือกจังหวัด</option>
                                @foreach ($provinces as $province)
                                    @if (empty($customer))
                                        <option value="{{$province->id}}">{{$province->name_th}}</option>
                                    @else
                                        @if ($province->id == $customer->cust_province)
                                            <option value="{{$province->id}}" selected>{{$province->name_th}}</option>
                                        @else
                                            <option value="{{$province->id}}">{{$province->name_th}}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="cust_district" class="">อำเภอ</label>
                            <select name="cust_district" id="cust_district" class="form-control" required onchange="finddistric(this)">
                                <option value="">เลือกอำเภอ</option>
                                @if (!empty($customer))
                                    @foreach ($amphures as $amphure)
                                        @if ($amphure->id == $customer->cust_district)
                                            <option value="{{$amphure->id}}" selected>{{$amphure->name_th}}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="cust_sub_district" class="">ตำบล</label>
                            <select name="cust_sub_district" id="cust_sub_district" class="form-control" required onchange="findzipcode(this)">
                                <option value="">เลือกตำบล</option>
                                @if (!empty($customer))
                                    @foreach ($Districts as $District)
                                        @if ($District->id == $customer->cust_sub_district)
                                            <option value="{{$District->id}}" selected>{{$District->name_th}}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="cust_phone" class="">เบอร์ติดต่อ</label>
                            <input name="cust_phone" id="cust_phone" placeholder="" type="text" class="form-control"
                            value="{{!empty($customer->cust_phone) ? $customer->cust_phone : null}}">
                        </div>
                    </div>
                </div>
                @if (!empty($customer->id))
                    <div class="d-block text-center"><br>
                        <button class="btn-wide  btn btn-success" type="submit">แก้ไขข้อมูลลูกค้า</button>
                    </div>
                @else
                    <div class="d-block text-center"><br>
                        <input type="hidden" name="flag" value="addInManagement">
                        <button class="btn-wide  btn btn-primary" type="submit">บันทึกข้อมูลลูกค้าใหม่</button>
                    </div>
                @endif
            </form>
            <a href="/get_customer_list/1">
                <button class="btn-wide  btn btn-light" type="reset" style="float: right;">กลับ</button>
            </a>
        </div>
    </div>
</div>

@endsection
<script>
    //     $.ajaxSetup({
    //     headers: {
    //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     }
    //   });
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
                        $('#cust_province').attr('readonly', true);
                        $('#cust_province').html('');
                        // $('#cust_province').append($("<option></option>").attr("value", '').text('เลือกจังหวัด')); 
                        $.each(data.provincesall, function(i, item) {
                            if(data.province[0].id != item.id){
                                // $('#cust_province').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
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
    </script>