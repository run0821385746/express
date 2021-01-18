@extends("welcome")
@section("content")

<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
            @if (!empty($dropcenter->id))
                <h5 class="card-title"> แก้ไขข้อมูลสาขา</h5>
                <form method="post" action="/dropcenter/{{$dropcenter->id}}">
                @method('PUT')
            @else
                <h5 class="card-title">เพิ่มสาขาใหม่</h5>
                <form method="post" action="/dropcenter">
            @endif  
            
                {{csrf_field()}}
                <div class="row">
                    <div class="col-lg-8 col-md-8">
                        <div class="position-relative form-group">
                            <label for="drop_center_name" class="">ชื่อ Drop center</label>
                            <input name="drop_center_name" id="drop_center_name" placeholder="" type="text"
                            class="form-control" value="{{!empty($dropcenter->drop_center_name) ? $dropcenter->drop_center_name : null}}" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="position-relative form-group">
                            <label for="drop_center_name_initial" class="">ชื่อย่อ</label>
                            <input name="drop_center_name_initial" id="drop_center_name_initial" placeholder="" type="text"
                            class="form-control" value="{{!empty($dropcenter->drop_center_name_initial) ? $dropcenter->drop_center_name_initial : null}}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="position-relative form-group">
                            <label for="drop_center_address" class="">ที่อยู่ติดต่อ</label>
                            <textarea name="drop_center_address" id="drop_center_address" placeholder="" type="text" class="form-control">{{!empty($dropcenter->drop_center_address) ? $dropcenter->drop_center_address : null}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        {{-- <div class="position-relative form-group">
                            <label for="drop_center_postcode" class="">รหัสไปรษณีย์</label>
                            <input type="text" class="form-control" name="drop_center_postcode" id="drop_center_postcode" value="{{!empty($dropcenter->drop_center_postcode) ? $dropcenter->drop_center_postcode : null}}" required readonly>
                        </div> --}}
                        <label for="drop_center_postcode" class="">รหัสไปรษณีย์</label>
                        <div class="input-group mb-3">
                            <input name="drop_center_postcode" id="drop_center_postcode" value="{{!empty($dropcenter->drop_center_postcode) ? $dropcenter->drop_center_postcode : null}}" type="text" class="form-control" required>
                            <div class="input-group-append">
                                <button class="btn btn-info" type="button" onclick="findaddress()">ค้นหา</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="drop_center_province" class="">จังหวัด</label>
                            <select name="drop_center_province" id="drop_center_province" class="form-control" required onchange="findamphure(this)">
                                <option value="">เลือกจังหวัด</option>
                                @foreach ($provinces as $province)
                                    @if (empty($dropcenter))
                                        <option value="{{$province->id}}">{{$province->name_th}}</option>
                                    @else
                                        @if ($province->id == $dropcenter->drop_center_province)
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
                            <label for="drop_center_district" class="">อำเภอ</label>
                            <select name="drop_center_district" id="drop_center_district" class="form-control" required onchange="finddistric(this)" readonly>
                                <option value="">เลือกอำเภอ</option>
                                @if (!empty($dropcenter))
                                    @foreach ($amphures as $amphure)
                                        @if ($amphure->id == $dropcenter->drop_center_district)
                                            <option value="{{$amphure->id}}" selected>{{$amphure->name_th}}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="drop_center_sub_district" class="">ตำบล</label>
                            <select name="drop_center_sub_district" id="drop_center_sub_district" class="form-control" required onchange="findzipcode(this)" readonly>
                                <option value="">เลือกตำบล</option>
                                @if (!empty($dropcenter))
                                    @foreach ($Districts as $District)
                                        @if ($District->id == $dropcenter->drop_center_sub_district)
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
                            <label for="drop_center_phone" class="">เบอร์ติดต่อ</label>
                            <input name="drop_center_phone" id="drop_center_phone" placeholder="" type="text"
                        class="form-control" value="{{!empty($dropcenter->drop_center_phone) ? $dropcenter->drop_center_phone : null}}" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                    </div>
                </div>
                @if (!empty($dropcenter->id))
                    <div class="d-block text-center"><br>
                        <button class="btn-wide  btn btn-success">บันทึกการแก้ไขข้อมูล</button>
                    </div>
                @else
                    <div class="d-block text-center"><br>
                        <button class="btn-wide  btn btn-primary">บันทึกข้อมูล</button>
                    </div>
                @endif
                
            </form>
            <a href="/dropcenter_get_list/1">
            <button class="btn-wide  btn btn-light" style="float: right;">กลับ</button>
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
            zipcode = $("#drop_center_postcode").val();
            if(zipcode != ""){
                $.ajax({
                    method:"POST",
                    url:"{{url('findaddress')}}",
                    dataType: 'json',
                    data:{"zipcode":zipcode, "_token": "{{ csrf_token() }}"},
                    success:function(data){
                        // console.log(data.province[0].name_th);
                        $('#drop_center_province').attr('readonly', true);
                        $('#drop_center_province').html('');
                        // $('#drop_center_province').append($("<option></option>").attr("value", '').text('เลือกจังหวัด')); 
                        $.each(data.provincesall, function(i, item) {
                            if(data.province[0].id != item.id){
                                // $('#drop_center_province').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
                            }else{
                                $('#drop_center_province').append($("<option></option>").attr("value", item.id).attr("selected","selected").text(item.name_th)); 
                            }
                        });

                        $('#drop_center_district').html('');
                        $.each(data.amphures, function(i, item) {
                            $('#drop_center_district').append($("<option></option>").attr("value", item.id).text(item.name_th));
                            $('#drop_center_district').attr('readonly', false);
                        });

                        $('#drop_center_sub_district').html('');
                        $.each(data.Districts, function(i, item) {
                            $('#drop_center_sub_district').append($("<option></option>").attr("value", item.id).text(item.name_th));
                            $('#drop_center_sub_district').attr('readonly', false);
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
                    $('#drop_center_district').html('');
                    $('#drop_center_district').append($("<option></option>").attr("value", '').text('เลือกอำเภอ')); 
                    $('#drop_center_district').attr('readonly', false);
                    $.each(data, function(i, item) {
                        $('#drop_center_district').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
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
                    $('#drop_center_sub_district').html('');
                    $('#drop_center_sub_district').append($("<option></option>").attr("value", '').text('กรุณาเลือกตำบล')); 
                    $('#drop_center_sub_district').attr('readonly', false);
                    $.each(data, function(i, item) {
                        $('#drop_center_sub_district').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
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
                    $('#drop_center_postcode').attr('readonly', false);
                    $("#drop_center_postcode").val(data.zip_code);
                }
            });
        }
    </script>